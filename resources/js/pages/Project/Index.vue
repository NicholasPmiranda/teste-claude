<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { CalendarDays, CheckCircle, Clock, Plus } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbs = [
  {
    title: 'Dashboard',
    href: route('dashboard')
  },
  {
    title: 'Projetos',
    href: route('projects.index')
  }
];

defineProps({
  projects: {
    type: Array,
    required: true
  }
});

const showNewProjectDialog = ref(false);

const form = useForm({
  name: '',
  description: '',
  start_date: '',
  end_date: ''
});

const submitForm = () => {
  form.post(route('projects.store'), {
    onSuccess: () => {
      showNewProjectDialog.value = false;
      form.reset();
    }
  });
};

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
</script>

<template>
  <Head title="Projetos" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-foreground">Projetos</h1>
        
        <Dialog v-model:open="showNewProjectDialog">
          <DialogTrigger as-child>
            <Button class="flex items-center gap-2">
              <Plus class="h-4 w-4" />
              Novo Projeto
            </Button>
          </DialogTrigger>
          <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
              <DialogTitle>Criar Novo Projeto</DialogTitle>
              <DialogDescription>
                Preencha os detalhes do novo projeto abaixo.
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
              </div>
              <DialogFooter>
                <Button type="button" variant="outline" @click="showNewProjectDialog = false">Cancelar</Button>
                <Button type="submit" :disabled="form.processing">Criar Projeto</Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </div>

      <div v-if="projects.length === 0" class="overflow-hidden shadow-sm sm:rounded-lg p-6 text-center ">
        <p>Nenhum projeto encontrado. Crie um novo projeto para começar.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card v-for="project in projects" :key="project.id" class="overflow-hidden">
          <CardHeader class="pb-3">
            <div class="flex justify-between items-start">
              <CardTitle class="text-xl font-semibold truncate">{{ project.name }}</CardTitle>
              <span :class="`text-xs px-2 py-1 rounded-full ${getStatusClass(project.status)}`">
                {{ project.status === 'in_progress' ? 'Em Progresso' : 
                   project.status === 'pending' ? 'Pendente' : 
                   project.status === 'completed' ? 'Concluído' : 'Arquivado' }}
              </span>
            </div>
          </CardHeader>
          <CardContent>
            <p class="text-sm text-gray-600 line-clamp-3 mb-4">{{ project.description || 'Sem descrição' }}</p>
            <div class="flex items-center text-sm  mb-2">
              <CalendarDays class="h-4 w-4 mr-2" />
              <span>Início: {{ formatDate(project.start_date) }}</span>
            </div>
            <div class="flex items-center text-sm  mb-2">
              <Clock class="h-4 w-4 mr-2" />
              <span>Término: {{ formatDate(project.end_date) }}</span>
            </div>
            <div class="flex items-center text-sm ">
              <CheckCircle class="h-4 w-4 mr-2" />
              <span>{{ project.tasks ? project.tasks.length : 0 }} tarefas</span>
            </div>
          </CardContent>
          <CardFooter class="bg-gray-50 flex justify-end gap-2 pt-4">
            <Link :href="route('projects.show', project.id)" class="inline-flex">
              <Button variant="outline" size="sm">Ver Detalhes</Button>
            </Link>
            <Link :href="route('projects.tasks.index', project.id)" class="inline-flex">
              <Button size="sm">Ver Tarefas</Button>
            </Link>
          </CardFooter>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
