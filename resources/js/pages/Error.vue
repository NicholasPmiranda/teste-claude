<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { AlertCircle } from 'lucide-vue-next';

defineProps({
  status: {
    type: Number,
    required: true
  },
  message: {
    type: String,
    required: true
  }
});
</script>

<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class=" overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="flex flex-col items-center justify-center text-center py-10">
          <div class="bg-red-50 p-3 rounded-full mb-4">
            <AlertCircle class="h-10 w-10 text-red-500" />
          </div>
          <h1 class="text-3xl font-bold text-foreground mb-2">Erro {{ status }}</h1>
          <p class="text-lg text-gray-600 mb-6">{{ message }}</p>
          <Link href="/" class="inline-flex">
            <Button>Voltar para o Início</Button>
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>
