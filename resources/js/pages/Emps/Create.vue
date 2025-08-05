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

defineProps<{
  depts: Array<{ id: number; name: string }>;
}>();

const form = useForm({
  name: '',
  email: '',
  birthday: '',
  depts_id: null as number | null,
});

const submit = () => {
  form.post(route('emps.store'));
};
</script>

<template>
  <Head title="社員 新規登録" />

  <AppLayout>
    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <Card>
          <CardHeader>
            <CardTitle>新しい社員を登録します</CardTitle>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit">
              <div class="grid gap-4">
                <div>
                  <Label for="name">氏名</Label>
                  <Input id="name" v-model="form.name" type="text" required />
                  <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
                </div>
                <div>
                  <Label for="email">Email</Label>
                  <Input id="email" v-model="form.email" type="email" required />
                  <div v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</div>
                </div>
                <div>
                  <Label for="birthday">生年月日</Label>
                  <Input id="birthday" v-model="form.birthday" type="date" required />
                  <div v-if="form.errors.birthday" class="text-sm text-red-600 mt-1">{{ form.errors.birthday }}</div>
                </div>
                <div>
                  <Label for="depts_id">所属</Label>
                  <Select v-model="form.depts_id">
                    <SelectTrigger>
                      <SelectValue placeholder="所属を選択" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem v-for="dept in depts" :key="dept.id" :value="dept.id">
                        {{ dept.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                   <div v-if="form.errors.depts_id" class="text-sm text-red-600 mt-1">{{ form.errors.depts_id }}</div>
                </div>
                <div class="flex justify-end">
                  <Button :disabled="form.processing">登録</Button>
                </div>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
