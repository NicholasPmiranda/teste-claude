<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { CheckCircle, Clock, AlertCircle, PauseCircle, BarChart } from 'lucide-vue-next';

const props = defineProps({
  project_id: {
    type: Number,
    required: true
  },
  metrics: {
    type: Object,
    required: true
  }
});

// Dados para o gráfico de status das tarefas
const statusData = [
  { label: 'Pendentes', value: props.metrics.pending_tasks, color: '#FEF3C7', textColor: '#92400E', icon: PauseCircle },
  { label: 'Em Progresso', value: props.metrics.in_progress_tasks, color: '#DBEAFE', textColor: '#1E40AF', icon: Clock },
  { label: 'Concluídas', value: props.metrics.completed_tasks, color: '#D1FAE5', textColor: '#065F46', icon: CheckCircle },
  { label: 'Bloqueadas', value: props.metrics.blocked_tasks, color: '#FEE2E2', textColor: '#B91C1C', icon: AlertCircle }
];

// Calcular o total de tarefas para o gráfico de pizza
const totalTasks = props.metrics.total_tasks;

// Função para calcular a porcentagem de cada status
const calculatePercentage = (value) => {
  if (totalTasks === 0) return 0;
  return Math.round((value / totalTasks) * 100);
};
</script>

<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="mb-6">
        <Link :href="route('projects.show', project_id)" class="text-sm text-gray-600 hover:text-foreground">
          &larr; Voltar para o Projeto
        </Link>
      </div>
      
      <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-2">
          <h1 class="text-2xl font-semibold text-foreground">Métricas do Projeto</h1>
          <BarChart class="h-6 w-6 " />
        </div>
      </div>

      <!-- Resumo das métricas -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium ">Total de Tarefas</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-3xl font-bold">{{ metrics.total_tasks }}</div>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium ">Tarefas Concluídas</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-3xl font-bold text-green-600">{{ metrics.completed_tasks }}</div>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium ">Em Progresso</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-3xl font-bold text-blue-600">{{ metrics.in_progress_tasks }}</div>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium ">Porcentagem Concluída</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="flex items-center">
              <div class="text-3xl font-bold">{{ metrics.completion_percentage }}%</div>
              <div class="ml-4 w-24 bg-gray-200 rounded-full h-2.5">
                <div 
                  class="bg-blue-600 h-2.5 rounded-full" 
                  :style="{ width: `${metrics.completion_percentage}%` }"
                ></div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Detalhamento por status -->
      <Card class="mb-8">
        <CardHeader>
          <CardTitle>Distribuição de Tarefas por Status</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div 
              v-for="(item, index) in statusData" 
              :key="index" 
              class="flex flex-col items-center p-4 rounded-lg"
              :style="{ backgroundColor: item.color }"
            >
              <component :is="item.icon" class="h-8 w-8 mb-2" :style="{ color: item.textColor }" />
              <div class="text-xl font-bold" :style="{ color: item.textColor }">{{ item.value }}</div>
              <div class="text-sm" :style="{ color: item.textColor }">{{ item.label }}</div>
              <div class="text-sm mt-1" :style="{ color: item.textColor }">
                {{ calculatePercentage(item.value) }}%
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Progresso geral -->
      <Card>
        <CardHeader>
          <CardTitle>Progresso Geral do Projeto</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="flex flex-col items-center">
            <div class="relative w-48 h-48 mb-6">
              <!-- Círculo de fundo -->
              <div class="absolute inset-0 rounded-full bg-gray-100"></div>
              
              <!-- Círculo de progresso -->
              <div 
                class="absolute inset-0 rounded-full bg-blue-500 overflow-hidden"
                :style="{
                  clipPath: `polygon(0 0, 100% 0, 100% 100%, 0% 100%, 0 0, 50% 50%, 50% 0, 50% 0, 50% 50%, 0 0)`
                }"
              ></div>
              
              <!-- Texto central -->
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                  <div class="text-3xl font-bold">{{ metrics.completion_percentage }}%</div>
                  <div class="text-sm ">Concluído</div>
                </div>
              </div>
            </div>
            
            <div class="w-full max-w-md bg-gray-200 rounded-full h-4 mb-2">
              <div 
                class="bg-blue-600 h-4 rounded-full" 
                :style="{ width: `${metrics.completion_percentage}%` }"
              ></div>
            </div>
            
            <div class="text-sm ">
              {{ metrics.completed_tasks }} de {{ metrics.total_tasks }} tarefas concluídas
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
