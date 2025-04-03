<script setup lang="ts">
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  accept?: string;
  maxFileSize?: number;
}>();

const emit = defineEmits<{
  (e: 'change', file: File): void;
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];

  if (file) {
    if (props.maxFileSize && file.size > props.maxFileSize) {
      alert(`O arquivo é muito grande. O tamanho máximo permitido é ${props.maxFileSize / 1024 / 1024} MB.`);
      return;
    }

    selectedFile.value = file;
    emit('change', file);
  }
};

const formattedFileSize = computed(() => {
  if (!selectedFile.value) return '';
  const size = selectedFile.value.size;
  if (size < 1024) return `${size} bytes`;
  if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} KB`;
  return `${(size / 1024 / 1024).toFixed(1)} MB`;
});
</script>

<template>
  <div class="relative">
    <input
      type="file"
      :accept="accept"
      @change="handleFileChange"
      ref="fileInput"
      class="absolute left-0 top-0 opacity-0 cursor-pointer"
    />
    <Button
      variant="outline"
      @click="fileInput?.click()"
    >
      <template v-if="!selectedFile">
        <span>Selecione um arquivo</span>
      </template>
      <template v-else>
        <span>{{ selectedFile.name }}</span>
        <span class="text-sm text-muted-foreground">({{ formattedFileSize }})</span>
      </template>
    </Button>
  </div>
</template>
