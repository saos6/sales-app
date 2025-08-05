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
  depts: Array<{
    id: number;
    name: string;
    parent_id: number | null;
    created_at: string;
    updated_at: string;
  }>;
}>();

const deleteDept = (id: number) => {
  if (confirm('本当に削除しますか？')) {
    router.delete(route('depts.destroy', id));
  }
};
</script>

<template>
  <Head title="所属マスタ" />

  <AppLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex justify-between mb-4">
              <Link :href="route('depts.create')">
                <Button>新規登録</Button>
              </Link>
              <a :href="route('depts.export')">
                <Button variant="outline">Excelエクスポート</Button>
              </a>
            </div>

            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>ID</TableHead>
                  <TableHead>所属名</TableHead>
                  <TableHead>親所属</TableHead>
                  <TableHead>操作</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="dept in depts" :key="dept.id">
                  <TableCell>{{ dept.id }}</TableCell>
                  <TableCell>{{ dept.name }}</TableCell>
                  <TableCell>{{ dept.parent?.name }}</TableCell>
                  <TableCell>
                    <Link :href="route('depts.edit', dept.id)" class="mr-2">
                      <Button variant="outline" size="sm">編集</Button>
                    </Link>
                    <Button variant="destructive" size="sm" @click="deleteDept(dept.id)">削除</Button>
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
