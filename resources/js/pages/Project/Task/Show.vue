<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { CalendarDays, Clock, Edit, Trash2, CheckCircle, MessageSquare, Paperclip, PlusCircle, AlertCircle, PauseCircle, XCircle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import AttachmentForm from '@/components/AttachmentForm.vue';

const props = defineProps({
  task: {
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
    title: 'Tarefas',
    href: route('projects.tasks.index', props.task.project_id)
  },
  {
    title: props.task.title,
    href: route('tasks.show', props.task.id)
  }
];

const showEditDialog = ref(false);
const showDeleteDialog = ref(false);
const showLogHoursDialog = ref(false);
const showCommentDialog = ref(false);
const currentReplyCommentId = ref<number | null>(null);

const form = useForm({
  title: props.task.title,
  description: props.task.description || '',
  start_date: props.task.start_date || '',
  due_date: props.task.due_date || '',
  priority: props.task.priority || 'medium',
  status: props.task.status || 'pending',
  estimated_hours: props.task.estimated_hours || '',
  tags: props.task.tags || []
});

const hoursForm = useForm({
  hours: ''
});

const commentForm = useForm({
  content: '',
  task_id: props.task.id,
  parent_id: null,
  mentioned_users: []
});

const submitForm = () => {
  form.put(route('tasks.update', props.task.id), {
    onSuccess: () => {
      showEditDialog.value = false;
    }
  });
};

const deleteTask = () => {
  router.delete(route('tasks.destroy', props.task.id), {
    onSuccess: () => {
      router.visit(route('projects.tasks.index', props.task.project_id));
    }
  });
};

const completeTask = () => {
  router.patch(route('tasks.complete', props.task.id), {
    onSuccess: () => {
      // Recarregar a página para mostrar o status atualizado
      router.reload();
    }
  });
};

const submitHours = () => {
  hoursForm.post(route('tasks.log-hours', props.task.id), {
    onSuccess: () => {
      showLogHoursDialog.value = false;
      hoursForm.reset();
    }
  });
};

const submitComment = () => {
  commentForm.post(route('comments.store'), {
    onSuccess: () => {
      showCommentDialog.value = false;
      commentForm.reset();
      commentForm.task_id = props.task.id;
      commentForm.parent_id = null;
      currentReplyCommentId.value = null;
    }
  });
};

const replyToComment = (commentId: number) => {
  currentReplyCommentId.value = commentId;
  commentForm.parent_id = commentId;
  showCommentDialog.value = true;
};

const statusOptions = [
  { value: 'pending' as const, label: 'Pendente' },
  { value: 'in_progress' as const, label: 'Em Progresso' },
  { value: 'completed' as const, label: 'Concluída' },
  { value: 'blocked' as const, label: 'Bloqueada' }
] as const;

const priorityOptions = [
  { value: 'low' as const, label: 'Baixa' },
  { value: 'medium' as const, label: 'Média' },
  { value: 'high' as const, label: 'Alta' },
  { value: 'urgent' as const, label: 'Urgente' }
] as const;

const getStatusClass = (status: typeof statusOptions[number]['value']) => {
  const statusClasses = {
    pending: 'bg-yellow-100 text-yellow-800',
    in_progress: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    blocked: 'bg-red-100 text-red-800'
  };
  
  return statusClasses[status] || 'bg-gray-100 text-gray-800';
};

const getPriorityClass = (priority: typeof priorityOptions[number]['value']) => {
  const priorityClasses = {
    low: 'bg-green-50 text-green-700',
    medium: 'bg-blue-50 text-blue-700',
    high: 'bg-orange-50 text-orange-700',
    urgent: 'bg-red-50 text-red-700'
  };
  
  return priorityClasses[priority] || 'bg-gray-50 text-gray-700';
};

const formatDate = (dateString: string | null | undefined) => {
  if (!dateString) return 'Não definida';
  
  const date = new Date(dateString);
  return date.toLocaleDateString('pt-BR');
};

const statusLabel = computed(() => {
  const status = statusOptions.find(option => option.value === props.task.status);
  return status ? status.label : 'Desconhecido';
});

const priorityLabel = computed(() => {
  const priority = priorityOptions.find(option => option.value === props.task.priority);
  return priority ? priority.label : 'Desconhecida';
});

const getStatusIcon = (status: typeof statusOptions[number]['value']) => {
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

const StatusIcon = computed(() => {
  return getStatusIcon(props.task.status);
});

const formatDateTime = (dateString: string | null | undefined) => {
  if (!dateString) return '';
  
  const date = new Date(dateString);
  return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
  <Head :title="task.title" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      
      <div class="bg-background shadow-sm rounded-lg overflow-hidden border border-border">
        <div class="p-6 border-b border-border">
          <div class="flex justify-between items-start">
            <div>
              <div class="flex items-center gap-2">
                <h1 class="text-2xl font-semibold text-foreground">{{ task.title }}</h1>
                <span :class="`inline-flex items-center gap-1 text-sm px-3 py-1 rounded-full ${getStatusClass(task.status)}`">
                  <component :is="StatusIcon" class="h-4 w-4" />
                  {{ statusLabel }}
                </span>
                <span :class="`text-sm px-3 py-1 rounded-full ${getPriorityClass(task.priority)}`">
                  {{ priorityLabel }}
                </span>
              </div>
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
                    <DialogTitle>Editar Tarefa</DialogTitle>
                    <DialogDescription>
                      Atualize os detalhes da tarefa abaixo.
                    </DialogDescription>
                  </DialogHeader>
                  <form @submit.prevent="submitForm">
                    <div class="grid gap-4 py-4">
                      <div class="grid gap-2">
                        <Label for="title">Título da Tarefa</Label>
                        <Input id="title" v-model="form.title" required />
                        <div v-if="form.errors.title" class="text-sm text-red-600">{{ form.errors.title }}</div>
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
                            <option v-for="option in priorityOptions" :key="option.value" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>
                          <div v-if="form.errors.priority" class="text-sm text-red-600">{{ form.errors.priority }}</div>
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
                      <div class="grid gap-2">
                        <Label for="estimated_hours">Horas Estimadas</Label>
                        <Input id="estimated_hours" type="number" min="0" step="0.5" v-model="form.estimated_hours" />
                        <div v-if="form.errors.estimated_hours" class="text-sm text-red-600">{{ form.errors.estimated_hours }}</div>
                      </div>
                    </div>
                    <DialogFooter>
                      <Button type="button" variant="outline" @click="showEditDialog = false">Cancelar</Button>
                      <Button type="submit" :disabled="form.processing">Salvar Alterações</Button>
                    </DialogFooter>
                  </form>
                </DialogContent>
              </Dialog>
              
              <Button 
                v-if="task.status !== 'completed'" 
                variant="outline" 
                class="flex items-center gap-2" 
                @click="completeTask"
              >
                <CheckCircle class="h-4 w-4" />
                Marcar como Concluída
              </Button>
              
              <Dialog v-model:open="showLogHoursDialog">
                <DialogTrigger as-child>
                  <Button variant="outline" class="flex items-center gap-2">
                    <Clock class="h-4 w-4" />
                    Registrar Horas
                  </Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-[425px]">
                  <DialogHeader>
                    <DialogTitle>Registrar Horas Trabalhadas</DialogTitle>
                    <DialogDescription>
                      Informe quantas horas você trabalhou nesta tarefa.
                    </DialogDescription>
                  </DialogHeader>
                  <form @submit.prevent="submitHours">
                    <div class="grid gap-4 py-4">
                      <div class="grid gap-2">
                        <Label for="hours">Horas Trabalhadas</Label>
                        <Input id="hours" type="number" min="0.1" step="0.1" v-model="hoursForm.hours" required />
                        <div v-if="hoursForm.errors.hours" class="text-sm text-red-600">{{ hoursForm.errors.hours }}</div>
                      </div>
                    </div>
                    <DialogFooter>
                      <Button type="button" variant="outline" @click="showLogHoursDialog = false">Cancelar</Button>
                      <Button type="submit" :disabled="hoursForm.processing">Registrar</Button>
                    </DialogFooter>
                  </form>
                </DialogContent>
              </Dialog>
              
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
                      Tem certeza que deseja excluir esta tarefa? Esta ação não pode ser desfeita e todos os comentários e anexos associados também serão excluídos.
                    </DialogDescription>
                  </DialogHeader>
                  <DialogFooter>
                    <Button type="button" variant="outline" @click="showDeleteDialog = false">Cancelar</Button>
                    <Button type="button" variant="destructive" @click="deleteTask">Excluir Tarefa</Button>
                  </DialogFooter>
                </DialogContent>
              </Dialog>
            </div>
          </div>
        </div>
        
        <div class="p-6 bg-background">
          <h2 class="text-lg font-medium text-foreground mb-4">Detalhes da Tarefa</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="text-sm font-medium text-muted-foreground mb-2">Descrição</h3>
              <p class="text-foreground">{{ task.description || 'Sem descrição' }}</p>
              
              <div class="mt-6">
                <h3 class="text-sm font-medium text-muted-foreground mb-2">Datas</h3>
                <div class="flex items-center text-sm text-foreground mb-2">
                  <CalendarDays class="h-4 w-4 mr-2" />
                  <span>Início: {{ formatDate(task.start_date) }}</span>
                </div>
                <div class="flex items-center text-sm text-foreground mb-2">
                  <Clock class="h-4 w-4 mr-2" />
                  <span>Entrega: {{ formatDate(task.due_date) }}</span>
                </div>
                <div v-if="task.completed_date" class="flex items-center text-sm text-foreground">
                  <CheckCircle class="h-4 w-4 mr-2" />
                  <span>Concluída em: {{ formatDate(task.completed_date) }}</span>
                </div>
              </div>
            </div>
            
            <div>
              <h3 class="text-sm font-medium text-muted-foreground mb-2">Progresso</h3>
              <div class="flex items-center justify-between text-sm text-foreground mb-2">
                <span>Horas Estimadas:</span>
                <span class="font-medium">{{ task.estimated_hours || 'Não definidas' }}</span>
              </div>
              <div class="flex items-center justify-between text-sm text-foreground">
                <span>Horas Trabalhadas:</span>
                <span class="font-medium">{{ task.actual_hours || '0' }}</span>
              </div>
              
              <div class="mt-6">
                <h3 class="text-sm font-medium text-muted-foreground mb-2">Tags</h3>
                <div class="flex flex-wrap gap-2">
                  <span 
                    v-for="(tag, index) in task.tags" 
                    :key="index" 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/20 text-primary"
                  >
                    {{ tag }}
                  </span>
                  <span v-if="!task.tags || task.tags.length === 0" class="text-sm text-muted-foreground">
                    Nenhuma tag definida
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Seção de Comentários -->
        <div class="p-6 border-t border-border">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-medium text-foreground">Comentários</h2>
            <Dialog v-model:open="showCommentDialog">
              <DialogTrigger as-child>
                <Button variant="outline" class="flex items-center gap-2">
                  <PlusCircle class="h-4 w-4" />
                  Adicionar Comentário
                </Button>
              </DialogTrigger>
              <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                  <DialogTitle>Adicionar Comentário</DialogTitle>
                  <DialogDescription>
                    Escreva seu comentário abaixo.
                  </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitComment">
                  <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                      <Label for="content">Conteúdo</Label>
                      <textarea 
                        id="content" 
                        v-model="commentForm.content"
                        class="flex min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        required
                      ></textarea>
                      <div v-if="commentForm.errors.content" class="text-sm text-red-600">{{ commentForm.errors.content }}</div>
                    </div>
                  </div>
                  <DialogFooter>
                    <Button type="button" variant="outline" @click="showCommentDialog = false">Cancelar</Button>
                    <Button type="submit" :disabled="commentForm.processing">Adicionar</Button>
                  </DialogFooter>
                </form>
              </DialogContent>
            </Dialog>
          </div>
          
          <div v-if="!task.comments || task.comments.length === 0" class="text-center py-4 text-muted-foreground">
            <p>Nenhum comentário encontrado. Adicione um comentário para iniciar a discussão.</p>
          </div>
          
          <div v-else class="space-y-4">
            <div v-for="comment in task.comments" :key="comment.id" class="bg-background p-4 rounded-lg shadow-sm border border-border">
              <div class="flex justify-between items-start mb-2">
                <div class="flex items-center">
                  <div class="font-medium text-foreground">{{ comment.user ? comment.user.name : 'Usuário' }}</div>
                  <span class="mx-2 text-muted-foreground">&bull;</span>
                  <div class="text-sm text-muted-foreground">{{ formatDateTime(comment.created_at) }}</div>
                </div>
                <div class="flex space-x-2">
                  <Button variant="ghost" size="sm" class="h-8 w-8 p-0">
                    <span class="sr-only">Editar</span>
                    <Edit class="h-4 w-4" />
                  </Button>
                  <Button variant="ghost" size="sm" class="h-8 w-8 p-0">
                    <span class="sr-only">Excluir</span>
                    <Trash2 class="h-4 w-4" />
                  </Button>
                </div>
              </div>
              <div class="text-foreground mb-2">{{ comment.content }}</div>
              
              <!-- Comentários aninhados -->
              <div v-if="comment.replies && comment.replies.length > 0" class="mt-4 pl-6 border-l-2 border-border space-y-4">
                <div v-for="reply in comment.replies" :key="reply.id" class="bg-secondary/20 p-3 rounded-lg">
                  <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center">
                      <div class="font-medium text-foreground">{{ reply.user ? reply.user.name : 'Usuário' }}</div>
                      <span class="mx-2 text-muted-foreground">&bull;</span>
                      <div class="text-sm text-muted-foreground">{{ formatDateTime(reply.created_at) }}</div>
                    </div>
                    <div class="flex space-x-2">
                      <Button variant="ghost" size="sm" class="h-8 w-8 p-0">
                        <span class="sr-only">Editar</span>
                        <Edit class="h-4 w-4" />
                      </Button>
                      <Button variant="ghost" size="sm" class="h-8 w-8 p-0">
                        <span class="sr-only">Excluir</span>
                        <Trash2 class="h-4 w-4" />
                      </Button>
                    </div>
                  </div>
                  <div class="text-foreground">{{ reply.content }}</div>
                </div>
              </div>
              
              <div class="mt-2 text-right">
                <Button variant="ghost" size="sm" class="text-xs" @click="replyToComment(comment.id)">
                  <MessageSquare class="h-3 w-3 mr-1" />
                  Responder
                </Button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Seção de Anexos -->
        <div class="p-6 border-t border-border">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-medium text-foreground">Anexos</h2>
            <AttachmentForm :task_id="task.id" />
          </div>
          
          <div v-if="!task.attachments || task.attachments.length === 0" class="text-center py-4 text-muted-foreground">
            <p>Nenhum anexo encontrado. Adicione um anexo para compartilhar arquivos.</p>
          </div>
          
          <div v-else class="space-y-2">
            <div v-for="attachment in task.attachments" :key="attachment.id" class="flex items-center justify-between p-3 rounded-lg border border-gray-200">
              <div class="flex items-center">
                <Paperclip class="h-5 w-5  mr-3" />
                <div>
                  <div class="font-medium text-foreground">{{ attachment.original_filename }}</div>
                  <div class="text-xs text-foreground flex items-center gap-2">
                    <span>{{ (attachment.file_size / 1024).toFixed(2) }} KB</span>
                    <span>&bull;</span>
                    <span>Versão {{ attachment.version }}</span>
                    <span>&bull;</span>
                    <span :class="`px-1.5 py-0.5 rounded-full ${getStatusClass(attachment.status)}`">
                      {{ attachment.status === 'pending' ? 'Pendente' : 
                         attachment.status === 'approved' ? 'Aprovado' : 'Rejeitado' }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="flex space-x-2">
                <Button variant="ghost" size="sm" class="h-8 px-2">
                  Download
                </Button>
                <Button variant="ghost" size="sm" class="h-8 w-8 p-0">
                  <span class="sr-only">Excluir</span>
                  <Trash2 class="h-4 w-4" />
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
