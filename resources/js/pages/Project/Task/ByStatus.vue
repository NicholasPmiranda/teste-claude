<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { CalendarDays, Clock, CheckCircle, AlertCircle, PauseCircle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
  project_id: {
    type: Number,
    required: true
  },
  status: {
    type: String,
    required: true
  },
  tasks: {
    type: Array,
    required: true
  }
});



const getStatusClass = (status) => {
  const statusClasses = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'in_progress': 'bg-blue-100 text-blue-800',
    'completed': 'bg-green-100 text-green-800',
    'blocked': 'bg-red-100 text-red-800'
  };
  
  return statusClasses[status] || 'bg-gray-100 text-gray-800';
};

const getPriorityClass = (priority) => {
  const priorityClasses = {
    'low': 'bg-green-50 text-green-700',
    'medium': 'bg-blue-50 text-blue-700',
    'high': 'bg-orange-50 text-orange-700',
    'urgent': 'bg-red-50 text-red-700'
  };
  
  return priorityClasses[priority] || 'bg-gray-50 text-gray-700';
};

const formatDate = (dateString) => {
  if (!dateString) return 'Não definida';
  
  const date = new Date(dateString);
  return date.toLocaleDateString('pt-BR');
};

const getStatusIcon = (status) => {
  switch (status) {
    case 'pending':
      return PauseCircle;
    case 'in_progress':
      return Clock;
    case 'completed':
      return CheckCircle;
    case 'blocked':
      return AlertCircle;
    default:
      return PauseCircle;
  }
};

const StatusIcon = getStatusIcon(props.status);

const getStatusLabel = (status) => {
  switch (status) {
    case 'pending':
      return 'Pendente';
    case 'in_progress':
      return 'Em Progresso';
    case 'completed':
      return 'Concluída';
    case 'blocked':
      return 'Bloqueada';
    default:
      return 'Desconhecido';
  }
};

const getPriorityLabel = (priority) => {
  switch (priority) {
    case 'low':
      return 'Baixa';
    case 'medium':
      return 'Média';
    case 'high':
      return 'Alta';
    case 'urgent':
      return 'Urgente';
    default:
      return 'Desconhecida';
  }
};

const breadcrumbs = [
  {
    title: 'Dashboard',
    href: route('dashboard')
  },
  {
    title: 'Projetos',
    href: route('projects.index')
  },
  {
    title: 'Tarefas',
    href: route('projects.tasks.index', props.project_id)
  },
  {
    title: getStatusLabel(props.status),
    href: route('projects.tasks.by-status', [props.project_id, props.status])
  }
];
</script>

<template>
  <Head :title="getStatusLabel(status) + ' - Tarefas'" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      
      <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-2">
          <h1 class="text-2xl font-semibold text-foreground">Tarefas com Status:</h1>
          <span :class="`inline-flex items-center gap-1 text-sm px-3 py-1 rounded-full ${getStatusClass(status)}`">
            <component :is="StatusIcon" class="h-4 w-4" />
            {{ getStatusLabel(status) }}
          </span>
        </div>
        
        <Link :href="route('projects.tasks.index', project_id)" class="inline-flex">
          <Button variant="outline" size="sm">Ver Todas as Tarefas</Button>
        </Link>
      </div>

      <div v-if="tasks.length === 0" class=" overflow-hidden shadow-sm sm:rounded-lg p-6 text-center ">
        <p>Nenhuma tarefa encontrada com o status "{{ getStatusLabel(status) }}".</p>
      </div>

      <div v-else class="space-y-4">
        <Card v-for="task in tasks" :key="task.id" class="overflow-hidden">
          <CardHeader class="pb-3">
            <div class="flex justify-between items-start">
              <CardTitle class="text-lg font-semibold">{{ task.title }}</CardTitle>
              <span :class="`text-xs px-2 py-1 rounded-full ${getPriorityClass(task.priority)}`">
                {{ getPriorityLabel(task.priority) }}
              </span>
            </div>
          </CardHeader>
          <CardContent>
            <p class="text-sm text-gray-600 mb-4">{{ task.description || 'Sem descrição' }}</p>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <div class="flex items-center text-sm  mb-2">
                  <CalendarDays class="h-4 w-4 mr-2" />
                  <span>Início: {{ formatDate(task.start_date) }}</span>
                </div>
                <div class="flex items-center text-sm ">
                  <Clock class="h-4 w-4 mr-2" />
                  <span>Entrega: {{ formatDate(task.due_date) }}</span>
                </div>
              </div>
              <div>
                <div class="flex items-center text-sm  mb-2">
                  <span class="font-medium mr-2">Horas Estimadas:</span>
                  <span>{{ task.estimated_hours || 'Não definidas' }}</span>
                </div>
                <div class="flex items-center text-sm ">
                  <span class="font-medium mr-2">Horas Trabalhadas:</span>
                  <span>{{ task.actual_hours || '0' }}</span>
                </div>
              </div>
            </div>
          </CardContent>
          <CardFooter class="bg-gray-50 flex justify-end gap-2 pt-4">
            <Link :href="route('tasks.show', task.id)" class="inline-flex">
              <Button variant="outline" size="sm">Ver Detalhes</Button>
            </Link>
          </CardFooter>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
