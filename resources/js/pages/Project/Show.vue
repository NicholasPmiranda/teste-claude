<script setup >
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { CalendarDays, Clock, Edit, Archive, Trash2, ListChecks, PlusCircle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
  project: {
    type: Object,
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
    title: props.project.name,
    href: route('projects.show', props.project.id)
  }
];

const showEditDialog = ref(false);
const showDeleteDialog = ref(false);

const form = useForm({
  name: props.project.name,
  description: props.project.description || '',
  start_date: props.project.start_date || '',
  end_date: props.project.end_date || '',
  status: props.project.status
});

const submitForm = () => {
  form.put(route('projects.update', props.project.id), {
    onSuccess: () => {
      showEditDialog.value = false;
    }
  });
};

const deleteProject = () => {
  router.delete(route('projects.destroy', props.project.id), {
    onSuccess: () => {
      router.visit(route('projects.index'));
    }
  });
};

const archiveProject = () => {
  router.patch(route('projects.archive', props.project.id), {
    onSuccess: () => {
      // Recarregar a página para mostrar o status atualizado
      router.reload();
    }
  });
};

const statusOptions = [
  { value: 'pending', label: 'Pendente' },
  { value: 'in_progress', label: 'Em Progresso' },
  { value: 'completed', label: 'Concluído' },
  { value: 'archived', label: 'Arquivado' }
];

const getStatusClass = (status) => {
  const statusClasses = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'in_progress': 'bg-blue-100 text-blue-800',
    'completed': 'bg-green-100 text-green-800',
    'archived': 'bg-gray-100 text-gray-800'
  };
  
  return statusClasses[status] || 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString) => {
  if (!dateString) return 'Não definida';
  
  const date = new Date(dateString);
  return date.toLocaleDateString('pt-BR');
};

const statusLabel = computed(() => {
  const status = statusOptions.find(option => option.value === props.project.status);
  return status ? status.label : 'Desconhecido';
});

const completionPercentage = computed(() => {
  if (!props.project.tasks || props.project.tasks.length === 0) return 0;
  
  const completedTasks = props.project.tasks.filter(task => task.status === 'completed').length;
  return Math.round((completedTasks / props.project.tasks.length) * 100);
});
</script>

<template>
  <Head :title="project.name" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      
      <div class=" shadow-sm rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
          <div class="flex justify-between items-start">
            <div>
              <h1 class="text-2xl font-semibold text-foreground">{{ project.name }}</h1>
              <span :class="`mt-2 inline-block text-sm px-3 py-1 rounded-full ${getStatusClass(project.status)}`">
                {{ statusLabel }}
              </span>
            </div>
            
            <div class="flex space-x-2">
              <Dialog v-model:open="showEditDialog">
                <DialogTrigger as-child>
                  <Button variant="outline" class="flex items-center gap-2">
                    <Edit class="h-4 w-4" />
                    Editar
                  </Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-[425px]">
                  <DialogHeader>
                    <DialogTitle>Editar Projeto</DialogTitle>
                    <DialogDescription>
                      Atualize os detalhes do projeto abaixo.
                    </DialogDescription>
                  </DialogHeader>
                  <form @submit.prevent="submitForm">
                    <div class="grid gap-4 py-4">
                      <div class="grid gap-2">
                        <Label for="name">Nome do Projeto</Label>
                        <Input id="name" v-model="form.name" required />
                        <div v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</div>
                      </div>
                      <div class="grid gap-2">
                        <Label for="description">Descrição</Label>
                        <textarea 
                          id="description" 
                          v-model="form.description"
                          class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        ></textarea>
                        <div v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</div>
                      </div>
                      <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                          <Label for="start_date">Data de Início</Label>
                          <Input id="start_date" type="date" v-model="form.start_date" />
                          <div v-if="form.errors.start_date" class="text-sm text-red-600">{{ form.errors.start_date }}</div>
                        </div>
                        <div class="grid gap-2">
                          <Label for="end_date">Data de Término</Label>
                          <Input id="end_date" type="date" v-model="form.end_date" />
                          <div v-if="form.errors.end_date" class="text-sm text-red-600">{{ form.errors.end_date }}</div>
                        </div>
                      </div>
                      <div class="grid gap-2">
                        <Label for="status">Status</Label>
                        <select 
                          id="status" 
                          v-model="form.status"
                          class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                          <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                          </option>
                        </select>
                        <div v-if="form.errors.status" class="text-sm text-red-600">{{ form.errors.status }}</div>
                      </div>
                    </div>
                    <DialogFooter>
                      <Button type="button" variant="outline" @click="showEditDialog = false">Cancelar</Button>
                      <Button type="submit" :disabled="form.processing">Salvar Alterações</Button>
                    </DialogFooter>
                  </form>
                </DialogContent>
              </Dialog>
              
              <Button v-if="project.status !== 'archived'" variant="outline" class="flex items-center gap-2" @click="archiveProject">
                <Archive class="h-4 w-4" />
                Arquivar
              </Button>
              
              <Dialog v-model:open="showDeleteDialog">
                <DialogTrigger as-child>
                  <Button variant="destructive" class="flex items-center gap-2">
                    <Trash2 class="h-4 w-4" />
                    Excluir
                  </Button>
                </DialogTrigger>
                <DialogContent>
                  <DialogHeader>
                    <DialogTitle>Confirmar Exclusão</DialogTitle>
                    <DialogDescription>
                      Tem certeza que deseja excluir este projeto? Esta ação não pode ser desfeita e todas as tarefas associadas também serão excluídas.
                    </DialogDescription>
                  </DialogHeader>
                  <DialogFooter>
                    <Button type="button" variant="outline" @click="showDeleteDialog = false">Cancelar</Button>
                    <Button type="button" variant="destructive" @click="deleteProject">Excluir Projeto</Button>
                  </DialogFooter>
                </DialogContent>
              </Dialog>
            </div>
          </div>
        </div>
        
        <div class="p-6 ">
          <h2 class="text-lg font-medium text-foreground mb-4">Detalhes do Projeto</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="text-sm font-medium text-gray-500 mb-2">Descrição</h3>
              <p class="">{{ project.description || 'Sem descrição' }}</p>
              
              <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Datas</h3>
                <div class="flex items-center text-sm  mb-2">
                  <CalendarDays class="h-4 w-4 mr-2" />
                  <span>Início: {{ formatDate(project.start_date) }}</span>
                </div>
                <div class="flex items-center text-sm ">
                  <Clock class="h-4 w-4 mr-2" />
                  <span>Término: {{ formatDate(project.end_date) }}</span>
                </div>
              </div>
            </div>
            
            <div>
              <h3 class="text-sm font-medium text-gray-500 mb-2">Progresso</h3>
              <div class="w-full  rounded-full h-2.5 mb-2">
                <div class="h-2.5 rounded-full" :style="{ width: `${completionPercentage}%` }"></div>
              </div>
              <p class="text-sm ">{{ completionPercentage }}% concluído</p>
              
              <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Tarefas</h3>
                <div class="flex items-center justify-between">
                  <div class="flex items-center text-sm ">
                    <ListChecks class="h-4 w-4 mr-2" />
                    <span>{{ project.tasks ? project.tasks.length : 0 }} tarefas no total</span>
                  </div>
                  <Link :href="route('projects.tasks.index', project.id)" class="text-sm  hover:text-blue-800">
                    Ver todas
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="p-6 border-t border-gray-200">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-medium text-foreground">Tarefas Recentes</h2>
            <Link :href="route('tasks.store')" method="get" class="flex items-center gap-1 text-sm  hover:text-blue-800">
              <PlusCircle class="h-4 w-4" />
              Nova Tarefa
            </Link>
          </div>
          
          <div v-if="!project.tasks || project.tasks.length === 0" class="text-center py-4 text-gray-500">
            <p>Nenhuma tarefa encontrada. Crie uma nova tarefa para começar.</p>
          </div>
          
          <div v-else class="space-y-4">
            <Card v-for="task in project.tasks.slice(0, 5)" :key="task.id" class="overflow-hidden">
              <CardHeader class="py-3">
                <div class="flex justify-between items-start">
                  <CardTitle class="text-base font-medium">{{ task.title }}</CardTitle>
                  <span :class="`text-xs px-2 py-1 rounded-full ${getStatusClass(task.status)}`">
                    {{ task.status === 'in_progress' ? 'Em Progresso' : 
                       task.status === 'pending' ? 'Pendente' : 
                       task.status === 'completed' ? 'Concluído' : 'Bloqueado' }}
                  </span>
                </div>
              </CardHeader>
              <CardContent class="py-3">
                <p class="text-sm text-gray-600 line-clamp-2">{{ task.description || 'Sem descrição' }}</p>
                <div class="mt-2 flex justify-between items-center">
                  <div class="flex items-center text-xs text-gray-500">
                    <Clock class="h-3 w-3 mr-1" />
                    <span>Prazo: {{ formatDate(task.due_date) }}</span>
                  </div>
                  <Link :href="route('tasks.show', task.id)" class="text-xs  hover:text-blue-800">
                    Ver detalhes
                  </Link>
                </div>
              </CardContent>
            </Card>
            
            <div v-if="project.tasks.length > 5" class="text-center pt-2">
              <Link :href="route('projects.tasks.index', project.id)" class="text-sm  hover:text-blue-800">
                Ver todas as {{ project.tasks.length }} tarefas
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
