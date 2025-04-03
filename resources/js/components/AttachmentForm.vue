<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogTrigger, DialogContent } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { FileUpload } from '@/components/ui/file-upload';
import { useForm } from '@inertiajs/vue3';

interface Props {
  task_id: number;
}

const props = defineProps<Props>();

const form = useForm({
  file: null as File | null,
  task_id: props.task_id,
});

const handleFileChange = (file: File) => {
  form.file = file;
};

const handleSubmit = () => {
  form.post(route('attachments.store'), {
    onSuccess: () => {
      form.reset();
    },
  });
};

const open = ref(false);
</script>

<template>
  <Dialog v-model:open="open">
    <DialogTrigger as-child>
      <Button variant="outline" class="flex items-center gap-2">
        <PlusCircle class="h-4 w-4" />
        Adicionar Anexo
      </Button>
    </DialogTrigger>
    
    <DialogContent class="sm:max-w-[425px]">
      <div class="grid gap-4 py-4">
        <div class="grid gap-2">
          <Label for="file">Selecione o arquivo</Label>
          <FileUpload @change="handleFileChange" />
        </div>
        
        <div class="flex justify-end gap-2">
          <Button variant="outline" @click="open = false">Cancelar</Button>
          <Button @click="handleSubmit">Adicionar</Button>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>
