<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';

defineProps<{
  emps: Array<{
    id: number;
    name: string;
    email: string;
    birthday: string;
    dept: { name: string } | null;
  }>;
}>();

const deleteEmp = (id: number) => {
  if (confirm('本当に削除しますか？')) {
    router.delete(route('emps.destroy', id));
  }
};
</script>

<template>
  <Head title="社員マスタ" />

  <AppLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex justify-between mb-4">
              <Link :href="route('emps.create')">
                <Button>新規登録</Button>
              </Link>
              <a :href="route('emps.export')">
                <Button variant="outline">Excelエクスポート</Button>
              </a>
            </div>

            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>ID</TableHead>
                  <TableHead>氏名</TableHead>
                  <TableHead>Email</TableHead>
                  <TableHead>生年月日</TableHead>
                  <TableHead>所属</TableHead>
                  <TableHead>操作</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="emp in emps" :key="emp.id">
                  <TableCell>{{ emp.id }}</TableCell>
                  <TableCell>{{ emp.name }}</TableCell>
                  <TableCell>{{ emp.email }}</TableCell>
                  <TableCell>{{ emp.birthday }}</TableCell>
                  <TableCell>{{ emp.dept?.name }}</TableCell>
                  <TableCell>
                    <Link :href="route('emps.edit', emp.id)" class="mr-2">
                      <Button variant="outline" size="sm">編集</Button>
                    </Link>
                    <Button variant="destructive" size="sm" @click="deleteEmp(emp.id)">削除</Button>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
