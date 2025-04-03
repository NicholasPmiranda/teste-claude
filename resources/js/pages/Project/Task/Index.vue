<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { CalendarDays, Clock, Plus, Filter, CheckCircle, AlertCircle, PauseCircle, XCircle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
  project_id: {
    type: Number,
    required: true
  },
  tasks: {
    type: Array,
    required: true
  }
});

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
  }
];

const showNewTaskDialog = ref(false);
const filterStatus = ref('all');
const filterPriority = ref('all');

const form = useForm({
  title: '',
  project_id: props.project_id,
  description: '',
  start_date: '',
  due_date: '',
  priority: 'medium',
  estimated_hours: '',
  tags: []
});

const submitForm = () => {
  form.post(route('tasks.store'), {
    onSuccess: () => {
      showNewTaskDialog.value = false;
      form.reset();
      form.project_id = props.project_id;
    }
  });
};

const filteredTasks = computed(() => {
  let result = [...props.tasks];
  
  if (filterStatus.value !== 'all') {
    result = result.filter(task => task.status === filterStatus.value);
  }
  
  if (filterPriority.value !== 'all') {
    result = result.filter(task => task.priority === filterPriority.value);
  }
  
  return result;
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
  if (!dateString) return 'Nu00e3o definida';
  
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
      return XCircle;
  }
};

const getStatusLabel = (status) => {
  switch (status) {
    case 'pending':
      return 'Pendente';
    case 'in_progress':
      return 'Em Progresso';
    case 'completed':
      return 'Concluu00edda';
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
      return 'Mu00e9dia';
    case 'high':
      return 'Alta';
    case 'urgent':
      return 'Urgente';
    default:
      return 'Desconhecida';
  }
};
</script>

<template>
  <Head title="Tarefas do Projeto" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-foreground">Tarefas do Projeto</h1>
        
        <div class="flex space-x-2">
          <div class="relative">
            <Button variant="outline" class="flex items-center gap-2">
              <Filter class="h-4 w-4" />
              Filtrar
            </Button>
            <div class="absolute right-0 mt-2 w-56  rounded-md shadow-lg overflow-hidden z-20 border border-gray-200 hidden group-hover:block">
              <div class="p-4">
                <div class="mb-4">
                  <Label for="status-filter" class="text-sm font-medium">Status</Label>
                  <select 
                    id="status-filter" 
                    v-model="filterStatus"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                  >
                    <option value="all">Todos</option>
                    <option value="pending">Pendente</option>
                    <option value="in_progress">Em Progresso</option>
                    <option value="completed">Concluu00edda</option>
                    <option value="blocked">Bloqueada</option>
                  </select>
                </div>
                <div>
                  <Label for="priority-filter" class="text-sm font-medium">Prioridade</Label>
                  <select 
                    id="priority-filter" 
                    v-model="filterPriority"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                  >
                    <option value="all">Todas</option>
                    <option value="low">Baixa</option>
                    <option value="medium">Mu00e9dia</option>
                    <option value="high">Alta</option>
                    <option value="urgent">Urgente</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          
          <Dialog v-model:open="showNewTaskDialog">
            <DialogTrigger as-child>
              <Button class="flex items-center gap-2">
                <Plus class="h-4 w-4" />
                Nova Tarefa
              </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
              <DialogHeader>
                <DialogTitle>Criar Nova Tarefa</DialogTitle>
                <DialogDescription>
                  Preencha os detalhes da nova tarefa abaixo.
                </DialogDescription>
              </DialogHeader>
              <form @submit.prevent="submitForm">
                <div class="grid gap-4 py-4">
                  <div class="grid gap-2">
                    <Label for="title">Tu00edtulo da Tarefa</Label>
                    <Input id="title" v-model="form.title" required />
                    <div v-if="form.errors.title" class="text-sm text-red-600">{{ form.errors.title }}</div>
                  </div>
                  <div class="grid gap-2">
                    <Label for="description">Descriu00e7u00e3o</Label>
                    <textarea 
                      id="description" 
                      v-model="form.description"
                      class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    ></textarea>
                    <div v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</div>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                      <Label for="start_date">Data de Inu00edcio</Label>
                      <Input id="start_date" type="date" v-model="form.start_date" />
                      <div v-if="form.errors.start_date" class="text-sm text-red-600">{{ form.errors.start_date }}</div>
                    </div>
                    <div class="grid gap-2">
                      <Label for="due_date">Data de Entrega</Label>
                      <Input id="due_date" type="date" v-model="form.due_date" />
                      <div v-if="form.errors.due_date" class="text-sm text-red-600">{{ form.errors.due_date }}</div>
                    </div>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                      <Label for="priority">Prioridade</Label>
                      <select 
                        id="priority" 
                        v-model="form.priority"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                      >
                        <option value="low">Baixa</option>
                        <option value="medium">Mu00e9dia</option>
                        <option value="high">Alta</option>
                        <option value="urgent">Urgente</option>
                      </select>
                      <div v-if="form.errors.priority" class="text-sm text-red-600">{{ form.errors.priority }}</div>
                    </div>
                    <div class="grid gap-2">
                      <Label for="estimated_hours">Horas Estimadas</Label>
                      <Input id="estimated_hours" type="number" min="0" step="0.5" v-model="form.estimated_hours" />
                      <div v-if="form.errors.estimated_hours" class="text-sm text-red-600">{{ form.errors.estimated_hours }}</div>
                    </div>
                  </div>
                </div>
                <DialogFooter>
                  <Button type="button" variant="outline" @click="showNewTaskDialog = false">Cancelar</Button>
                  <Button type="submit" :disabled="form.processing">Criar Tarefa</Button>
                </DialogFooter>
              </form>
            </DialogContent>
          </Dialog>
        </div>
      </div>

      <div v-if="filteredTasks.length === 0" class=" overflow-hidden shadow-sm sm:rounded-lg p-6 text-center ">
        <p v-if="tasks.length === 0">Nenhuma tarefa encontrada. Crie uma nova tarefa para comeu00e7ar.</p>
        <p v-else>Nenhuma tarefa corresponde aos filtros selecionados.</p>
      </div>

      <div v-else class="space-y-4">
        <Card v-for="task in filteredTasks" :key="task.id" class="overflow-hidden">
          <CardHeader class="pb-3">
            <div class="flex justify-between items-start">
              <CardTitle class="text-lg font-semibold">{{ task.title }}</CardTitle>
              <div class="flex space-x-2">
                <span :class="`text-xs px-2 py-1 rounded-full ${getPriorityClass(task.priority)}`">
                  {{ getPriorityLabel(task.priority) }}
                </span>
                <span :class="`text-xs px-2 py-1 rounded-full ${getStatusClass(task.status)}`">
                  {{ getStatusLabel(task.status) }}
                </span>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <p class="text-sm text-gray-600 mb-4">{{ task.description || 'Sem descriu00e7u00e3o' }}</p>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <div class="flex items-center text-sm  mb-2">
                  <CalendarDays class="h-4 w-4 mr-2" />
                  <span>Inu00edcio: {{ formatDate(task.start_date) }}</span>
                </div>
                <div class="flex items-center text-sm ">
                  <Clock class="h-4 w-4 mr-2" />
                  <span>Prazo: {{ formatDate(task.due_date) }}</span>
                </div>
              </div>
              <div>
                <div class="flex items-center text-sm  mb-2">
                  <span class="font-medium mr-2">Horas Estimadas:</span>
                  <span>{{ task.estimated_hours || 'Nu00e3o definidas' }}</span>
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
