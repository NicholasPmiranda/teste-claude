<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Briefcase, CheckSquare, Clock, AlertCircle, BarChart, ListChecks } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Dados de exemplo para projetos recentes
const recentProjects = ref([]);

// Dados de exemplo para tarefas pendentes
const pendingTasks = ref([]);

// Dados de exemplo para estatísticas
const stats = ref({
    total_projects: 0,
    active_projects: 0,
    completed_projects: 0,
    total_tasks: 0,
    pending_tasks: 0,
    completed_tasks: 0
});

// Função para carregar dados do dashboard
const loadDashboardData = async () => {
    try {
        // Em um cenário real, você faria uma chamada API aqui
        // Exemplo: const response = await axios.get('/api/dashboard');
        
        // Simulando dados para demonstração
        stats.value = {
            total_projects: 5,
            active_projects: 3,
            completed_projects: 2,
            total_tasks: 25,
            pending_tasks: 15,
            completed_tasks: 10
        };
        
        recentProjects.value = [
            { id: 1, name: 'Sistema de Gerenciamento', status: 'in_progress', completion_percentage: 65, tasks_count: 12 },
            { id: 2, name: 'Aplicativo Mobile', status: 'pending', completion_percentage: 20, tasks_count: 8 },
            { id: 3, name: 'Redesign do Website', status: 'completed', completion_percentage: 100, tasks_count: 5 }
        ];
        
        pendingTasks.value = [
            { id: 1, title: 'Implementar autenticação', project_id: 1, project_name: 'Sistema de Gerenciamento', priority: 'high', due_date: '2025-04-10' },
            { id: 2, title: 'Criar telas de dashboard', project_id: 1, project_name: 'Sistema de Gerenciamento', priority: 'medium', due_date: '2025-04-15' },
            { id: 3, title: 'Desenvolver API REST', project_id: 2, project_name: 'Aplicativo Mobile', priority: 'urgent', due_date: '2025-04-05' },
            { id: 4, title: 'Testar funcionalidades', project_id: 2, project_name: 'Aplicativo Mobile', priority: 'low', due_date: '2025-04-20' }
        ];
    } catch (error) {
        console.error('Erro ao carregar dados do dashboard:', error);
    }
};

// Função para obter a classe CSS baseada no status do projeto
const getStatusClass = (status) => {
    const statusClasses = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'in_progress': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'archived': 'bg-gray-100 text-gray-800'
    };
    
    return statusClasses[status] || 'bg-gray-100 text-gray-800';
};

// Função para obter a classe CSS baseada na prioridade da tarefa
const getPriorityClass = (priority) => {
    const priorityClasses = {
        'low': 'bg-green-50 text-green-700',
        'medium': 'bg-blue-50 text-blue-700',
        'high': 'bg-orange-50 text-orange-700',
        'urgent': 'bg-red-50 text-red-700'
    };
    
    return priorityClasses[priority] || 'bg-gray-50 text-gray-700';
};

// Função para formatar datas
const formatDate = (dateString) => {
    if (!dateString) return 'Não definida';
    
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
};

// Carregar dados ao montar o componente
onMounted(() => {
    loadDashboardData();
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Estatísticas Gerais -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium ">Projetos</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center">
                            <Briefcase class="h-8 w-8 text-blue-500 mr-4" />
                            <div>
                                <div class="text-3xl font-bold">{{ stats.total_projects }}</div>
                                <div class="text-sm ">Total de Projetos</div>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                            <div class="flex items-center">
                                <div class="h-2 w-2 rounded-full bg-blue-500 mr-2"></div>
                                <span>{{ stats.active_projects }} Ativos</span>
                            </div>
                            <div class="flex items-center">
                                <div class="h-2 w-2 rounded-full bg-green-500 mr-2"></div>
                                <span>{{ stats.completed_projects }} Concluídos</span>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter class="pt-0">
                        <Link :href="route('projects.index')" class="text-sm text-blue-600 hover:text-blue-800">
                            Ver todos os projetos →
                        </Link>
                    </CardFooter>
                </Card>
                
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium ">Tarefas</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center">
                            <ListChecks class="h-8 w-8 text-purple-500 mr-4" />
                            <div>
                                <div class="text-3xl font-bold">{{ stats.total_tasks }}</div>
                                <div class="text-sm ">Total de Tarefas</div>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                            <div class="flex items-center">
                                <div class="h-2 w-2 rounded-full bg-yellow-500 mr-2"></div>
                                <span>{{ stats.pending_tasks }} Pendentes</span>
                            </div>
                            <div class="flex items-center">
                                <div class="h-2 w-2 rounded-full bg-green-500 mr-2"></div>
                                <span>{{ stats.completed_tasks }} Concluídas</span>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter class="pt-0">
                        <Link :href="route('tasks.by-user', { user_id: 1 })" class="text-sm text-blue-600 hover:text-blue-800">
                            Ver minhas tarefas →
                        </Link>
                    </CardFooter>
                </Card>
                
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium ">Progresso Geral</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center">
                            <BarChart class="h-8 w-8 text-green-500 mr-4" />
                            <div>
                                <div class="text-3xl font-bold">
                                    {{ stats.total_tasks > 0 ? Math.round((stats.completed_tasks / stats.total_tasks) * 100) : 0 }}%
                                </div>
                                <div class="text-sm ">Tarefas Concluídas</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                                <div 
                                    class="bg-green-600 h-2.5 rounded-full" 
                                    :style="{ width: `${stats.total_tasks > 0 ? Math.round((stats.completed_tasks / stats.total_tasks) * 100) : 0}%` }"
                                ></div>
                            </div>
                            <div class="text-xs  text-right">
                                {{ stats.completed_tasks }} de {{ stats.total_tasks }} tarefas
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter class="pt-0">
                        <Button variant="outline" size="sm" class="w-full" @click="loadDashboardData">
                            Atualizar Dados
                        </Button>
                    </CardFooter>
                </Card>
            </div>
            
            <!-- Projetos Recentes e Tarefas Pendentes -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Projetos Recentes -->
                <Card>
                    <CardHeader>
                        <div class="flex justify-between items-center">
                            <CardTitle class="text-lg">Projetos Recentes</CardTitle>
                            <Link :href="route('projects.index')">
                                <Button variant="outline" size="sm">Ver Todos</Button>
                            </Link>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="recentProjects.length === 0" class="text-center py-6 ">
                            <p>Nenhum projeto encontrado.</p>
                        </div>
                        
                        <div v-else class="space-y-4">
                            <div 
                                v-for="project in recentProjects" 
                                :key="project.id" 
                                class="p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <Link 
                                        :href="route('projects.show', project.id)" 
                                        class="text-lg font-medium text-foreground hover:text-blue-600"
                                    >
                                        {{ project.name }}
                                    </Link>
                                    <span :class="`text-xs px-2 py-1 rounded-full ${getStatusClass(project.status)}`">
                                        {{ project.status === 'in_progress' ? 'Em Progresso' : 
                                           project.status === 'pending' ? 'Pendente' : 
                                           project.status === 'completed' ? 'Concluído' : 'Arquivado' }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center text-sm  mb-3">
                                    <ListChecks class="h-4 w-4 mr-1" />
                                    <span>{{ project.tasks_count }} tarefas</span>
                                </div>
                                
                                <div class="w-full bg-gray-200 rounded-full h-1.5 mb-1">
                                    <div 
                                        class="bg-blue-600 h-1.5 rounded-full" 
                                        :style="{ width: `${project.completion_percentage}%` }"
                                    ></div>
                                </div>
                                <div class="text-xs  text-right">
                                    {{ project.completion_percentage }}% concluído
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
                
                <!-- Tarefas Pendentes -->
                <Card>
                    <CardHeader>
                        <div class="flex justify-between items-center">
                            <CardTitle class="text-lg">Tarefas Pendentes</CardTitle>
                            <Button variant="outline" size="sm" @click="router.visit(route('tasks.by-user', { user_id: 1 }))">
                                Ver Todas
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="pendingTasks.length === 0" class="text-center py-6 ">
                            <p>Nenhuma tarefa pendente encontrada.</p>
                        </div>
                        
                        <div v-else class="space-y-3">
                            <div 
                                v-for="task in pendingTasks" 
                                :key="task.id" 
                                class="p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <Link 
                                        :href="route('tasks.show', task.id)" 
                                        class="text-base font-medium text-foreground hover:text-blue-600"
                                    >
                                        {{ task.title }}
                                    </Link>
                                    <span :class="`text-xs px-2 py-1 rounded-full ${getPriorityClass(task.priority)}`">
                                        {{ task.priority === 'low' ? 'Baixa' : 
                                           task.priority === 'medium' ? 'Média' : 
                                           task.priority === 'high' ? 'Alta' : 'Urgente' }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center text-xs  mb-1">
                                    <Briefcase class="h-3 w-3 mr-1" />
                                    <Link 
                                        :href="route('projects.show', task.project_id)" 
                                        class="hover:text-blue-600"
                                    >
                                        {{ task.project_name }}
                                    </Link>
                                </div>
                                
                                <div class="flex items-center text-xs ">
                                    <Clock class="h-3 w-3 mr-1" />
                                    <span>Prazo: {{ formatDate(task.due_date) }}</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
            
            <!-- Links Rápidos -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Links Rápidos</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <Link :href="route('projects.index')" class="block">
                            <div class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                <Briefcase class="h-8 w-8 text-blue-600 mb-2" />
                                <span class="text-sm font-medium text-blue-700">Projetos</span>
                            </div>
                        </Link>
                        
                        <Link :href="route('tasks.by-user', { user_id: 1 })" class="block">
                            <div class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                                <CheckSquare class="h-8 w-8 text-purple-600 mb-2" />
                                <span class="text-sm font-medium text-purple-700">Minhas Tarefas</span>
                            </div>
                        </Link>
                        
                        <div 
                            class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors cursor-pointer"
                            @click="router.visit('/projects/1/tasks/status/completed')"
                        >
                            <CheckSquare class="h-8 w-8 text-green-600 mb-2" />
                            <span class="text-sm font-medium text-green-700">Tarefas Concluídas</span>
                        </div>
                        
                        <div 
                            class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors cursor-pointer"
                            @click="router.visit('/projects/1')"
                        >
                            <BarChart class="h-8 w-8 text-orange-600 mb-2" />
                            <span class="text-sm font-medium text-orange-700">Métricas</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
