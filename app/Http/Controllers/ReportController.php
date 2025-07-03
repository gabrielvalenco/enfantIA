<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $period = $request->period ?? 'week';
        $startDate = null;
        $endDate = null;
        $dateFormat = '';
        $groupByFormat = '';
        $isAjaxRequest = $request->ajax();
        
        // Configurar datas e formatos de acordo com o período selecionado
        switch ($period) {
            case 'day':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $dateFormat = 'd/m';
                $groupByFormat = 'Y-m-d';
                $chartLabel = 'Últimos 7 dias';
                break;
                
            case 'week':
                $startDate = Carbon::now()->subWeeks(4)->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $dateFormat = 'd/m';
                $groupByFormat = 'Y-W'; // Ano-Semana
                $chartLabel = 'Últimas 5 semanas';
                break;
                
            case 'month':
                $startDate = Carbon::now()->subMonths(5)->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $dateFormat = 'M/Y';
                $groupByFormat = 'Y-m';
                $chartLabel = 'Últimos 6 meses';
                break;
        }
        
        // Obter tarefas completadas por período
        $completedTasks = Task::where('user_id', $user->id)
            ->where('status', true)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->get();
            
        // Obter tarefas criadas por período
        $createdTasks = Task::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        
        // Calcular tempo médio para conclusão de tarefas (em horas)
        $avgCompletionTimes = [];
        foreach ($completedTasks as $task) {
            $createdAt = Carbon::parse($task->created_at);
            $completedAt = Carbon::parse($task->updated_at); // Usando updated_at como referência de conclusão
            $groupKey = $createdAt->format($groupByFormat);
            
            if (!isset($avgCompletionTimes[$groupKey])) {
                $avgCompletionTimes[$groupKey] = ['total_hours' => 0, 'count' => 0];
            }
            
            $hours = $createdAt->diffInHours($completedAt);
            $avgCompletionTimes[$groupKey]['total_hours'] += $hours;
            $avgCompletionTimes[$groupKey]['count']++;
        }

        // Agrupar tarefas por período
        $completedByPeriod = [];
        $createdByPeriod = [];
        
        // Inicializar períodos
        $dateRange = CarbonPeriod::create($startDate, '1 day', $endDate);
        $periods = [];

        if ($period === 'day') {
            foreach ($dateRange as $date) {
                $key = $date->format($groupByFormat);
                $label = $date->format($dateFormat);
                $periods[$key] = $label;
                $completedByPeriod[$key] = 0;
                $createdByPeriod[$key] = 0;
            }
        } elseif ($period === 'week') {
            $weekRange = CarbonPeriod::create($startDate, '1 week', $endDate);
            foreach ($weekRange as $date) {
                $key = $date->format($groupByFormat);
                $weekStart = $date->startOfWeek()->format('d/m');
                $weekEnd = $date->endOfWeek()->format('d/m');
                $label = $weekStart . ' - ' . $weekEnd;
                $periods[$key] = $label;
                $completedByPeriod[$key] = 0;
                $createdByPeriod[$key] = 0;
            }
        } else { // month
            $monthRange = CarbonPeriod::create($startDate, '1 month', $endDate);
            foreach ($monthRange as $date) {
                if ($date->day === 1) { // Só processar o primeiro dia de cada mês
                    $key = $date->format($groupByFormat);
                    $label = $date->format($dateFormat);
                    $periods[$key] = $label;
                    $completedByPeriod[$key] = 0;
                    $createdByPeriod[$key] = 0;
                }
            }
        }
        
        // Contabilizar tarefas por período
        foreach ($completedTasks as $task) {
            $key = Carbon::parse($task->updated_at)->format($groupByFormat);
            if (isset($completedByPeriod[$key])) {
                $completedByPeriod[$key]++;
            }
        }
        
        foreach ($createdTasks as $task) {
            $key = Carbon::parse($task->created_at)->format($groupByFormat);
            if (isset($createdByPeriod[$key])) {
                $createdByPeriod[$key]++;
            }
        }
        
        // Calcular produtividade por categoria
        $categories = Category::where('user_id', $user->id)->get();
        $categoriesData = [];
        
        foreach ($categories as $category) {
            $completedCount = Task::where('user_id', $user->id)
                ->where('status', true)
                ->whereHas('categories', function ($query) use ($category) {
                    $query->where('categories.id', $category->id);
                })
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->count();
            
            if ($completedCount > 0) {
                $categoriesData[] = [
                    'name' => $category->name,
                    'count' => $completedCount,
                    'color' => $category->color
                ];
            }
        }
        
        // Calcular taxa de conclusão diária
        $completionRate = [];
        foreach ($periods as $key => $label) {
            $created = $createdByPeriod[$key] ?? 0;
            $completed = $completedByPeriod[$key] ?? 0;
            
            $rate = $created > 0 ? round(($completed / $created) * 100) : 0;
            $completionRate[$key] = $rate;
        }
        
        // Preparar dados para os gráficos
        $avgTimeData = [];
        foreach ($periods as $key => $label) {
            if (isset($avgCompletionTimes[$key]) && $avgCompletionTimes[$key]['count'] > 0) {
                $avgTimeData[$key] = round($avgCompletionTimes[$key]['total_hours'] / $avgCompletionTimes[$key]['count']);
            } else {
                $avgTimeData[$key] = 0;
            }
        }
        
        // Preparar dados de categorias
        $preparedCategoriesData = [
            'labels' => [],
            'data' => [],
            'colors' => []
        ];
        
        foreach ($categoriesData as $category) {
            $preparedCategoriesData['labels'][] = $category['name'];
            $preparedCategoriesData['data'][] = $category['count'];
            $preparedCategoriesData['colors'][] = $category['color'];
        }
        
        // Formatar saída conforme o tipo de requisição
        if ($isAjaxRequest) {
            // Converter arrays associativos para arrays indexados para melhor manipulação no JavaScript
            return response()->json([
                'periods' => array_values($periods),
                'created' => array_values($createdByPeriod),
                'completed' => array_values($completedByPeriod),
                'rates' => array_values($completionRate),
                'times' => array_values($avgTimeData),
                'categories' => $preparedCategoriesData,
                'totalTasks' => array_sum($createdByPeriod),
                'totalCompleted' => array_sum($completedByPeriod),
                'avgCompletionRate' => array_sum($createdByPeriod) > 0 ? round((array_sum($completedByPeriod) / array_sum($createdByPeriod)) * 100) : 0,
                'avgTime' => count($avgTimeData) > 0 ? round(array_sum($avgTimeData) / count(array_filter($avgTimeData))) : 0
            ]);
        }
        
        // Para requisições normais, retornar a view
        return view('reports.index', compact(
            'period',
            'chartLabel',
            'periods',
            'completedByPeriod',
            'createdByPeriod',
            'avgCompletionTimes',
            'categoriesData',
            'completionRate'
        ));
    }
}
