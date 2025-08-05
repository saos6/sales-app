<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';

const props = defineProps<{
  dept: {
    id: number;
    name: string;
    parent_id: number | null;
  };
  depts: Array<{ id: number; name: string }>;
}>();

const form = useForm({
  name: props.dept.name,
  parent_id: props.dept.parent_id,
});

const submit = () => {
  form.put(route('depts.update', props.dept.id));
};
</script>

<template>
  <Head title="所属 編集" />

  <AppLayout>
    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <Card>
          <CardHeader>
            <CardTitle>所属情報を編集します</CardTitle>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit">
              <div class="grid gap-4">
                <div>
                  <Label for="name">所属名</Label>
                  <Input id="name" v-model="form.name" type="text" required />
                  <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
                </div>
                <div>
                  <Label for="parent_id">親所属</Label>
                   <Select v-model="form.parent_id">
                    <SelectTrigger>
                      <SelectValue placeholder="親所属を選択" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem :value="null">
                        なし
                      </SelectItem>
                      <SelectItem v-for="d in depts" :key="d.id" :value="d.id">
                        {{ d.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                  <div v-if="form.errors.parent_id" class="text-sm text-red-600 mt-1">{{ form.errors.parent_id }}</div>
                </div>
                <div class="flex justify-end">
                  <Button :disabled="form.processing">更新</Button>
                </div>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>