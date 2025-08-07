<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { reactive, watch } from 'vue';
import { ChevronsUpDown, ArrowUp, ArrowDown } from 'lucide-vue-next';

const props = defineProps<{
  emps: {
    data: Array<{
      id: number;
      name: string;
      email: string;
      birthday: string;
      dept: { name: string } | null;
    }>;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    meta: { current_page: number; per_page: number; total: number };
  };
  filters: { search: string; per_page: string };
  sort?: string;
  order?: string;
}>();

const form = useForm({
  search: props.filters.search,
  per_page: props.filters.per_page || '10',
});

const search = () => {
  form.get(route('emps.index'), { preserveState: true });
};

watch(() => form.per_page, (newValue) => {
  router.get(route('emps.index'), { ...route().params, per_page: newValue }, { preserveState: true });
});

const deleteEmp = (id: number) => {
  if (confirm('本当に削除しますか？')) {
    router.delete(route('emps.destroy', id), {
      onSuccess: () => {
        // You can show a notification here or emit an event to the parent component if needed.
      },
    });
  }
};

const columns = reactive([
  { key: 'id', label: 'ID', visible: true, sortable: true },
  { key: 'name', label: '氏名', visible: true, sortable: true },
  { key: 'email', label: 'Email', visible: true, sortable: true },
  { key: 'birthday', label: '生年月日', visible: true, sortable: true },
  { key: 'dept', label: '所属', visible: true, sortable: false },
  { key: 'actions', label: '操作', visible: true, sortable: false },
]);

const isVisible = (key: string) => columns.find(c => c.key === key)?.visible;

const sortBy = (key: string) => {
  if (!columns.find(c => c.key === key)?.sortable) return;

  let newOrder = 'asc';
  if (props.sort === key && props.order === 'asc') {
    newOrder = 'desc';
  }

  router.get(route('emps.index'), { ...route().params, sort: key, order: newOrder }, { preserveState: true });
};

const sortIcon = (key: string) => {
  if (props.sort !== key) return ChevronsUpDown;
  if (props.order === 'asc') return ArrowUp;
  return ArrowDown;
};

</script>

<template>
  <Head title="社員マスタ" />

  <AppLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="mb-4">
              <form @submit.prevent="search">
                <div class="flex gap-2">
                  <Input v-model="form.search" placeholder="氏名、Emailで検索..." class="max-w-xs" />
                  <Button type="submit">検索</Button>
                </div>
              </form>
            </div>

            <div class="flex justify-between items-center mb-4">
              <div class="flex gap-2">
                <Link :href="route('emps.create')">
                  <Button>新規登録</Button>
                </Link>
                <a :href="route('emps.export', { search: form.search })">
                  <Button variant="outline">Excelエクスポート</Button>
                </a>
              </div>
              <div class="flex items-center gap-4">
                <Select v-model="form.per_page">
                  <SelectTrigger class="w-[100px]">
                    <SelectValue placeholder="表示件数" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="10">10件</SelectItem>
                    <SelectItem value="50">50件</SelectItem>
                    <SelectItem value="100">100件</SelectItem>
                  </SelectContent>
                </Select>
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button variant="outline">表示項目</Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent>
                    <DropdownMenuLabel>表示する列を選択</DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuCheckboxItem
                      v-for="column in columns"
                      :key="column.key"
                      v-model:checked="column.visible"
                    >
                      {{ column.label }}
                    </DropdownMenuCheckboxItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </div>
            </div>

            <div v-if="emps.meta" class="text-sm text-gray-500 mb-4">
              {{ emps.meta.total }}件見つかりました。
            </div>

            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead v-if="isVisible('id')" @click="sortBy('id')" class="cursor-pointer">
                    <div class="flex items-center">
                      ID <component :is="sortIcon('id')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('name')" @click="sortBy('name')" class="cursor-pointer">
                     <div class="flex items-center">
                      氏名 <component :is="sortIcon('name')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('email')" @click="sortBy('email')" class="cursor-pointer">
                     <div class="flex items-center">
                      Email <component :is="sortIcon('email')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('birthday')" @click="sortBy('birthday')" class="cursor-pointer">
                     <div class="flex items-center">
                      生年月日 <component :is="sortIcon('birthday')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('dept')">所属</TableHead>
                  <TableHead v-if="isVisible('actions')">操作</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="emp in emps.data" :key="emp.id">
                  <TableCell v-if="isVisible('id')">{{ emp.id }}</TableCell>
                  <TableCell v-if="isVisible('name')">{{ emp.name }}</TableCell>
                  <TableCell v-if="isVisible('email')">{{ emp.email }}</TableCell>
                  <TableCell v-if="isVisible('birthday')">{{ emp.birthday }}</TableCell>
                  <TableCell v-if="isVisible('dept')">{{ emp.dept?.name }}</TableCell>
                  <TableCell v-if="isVisible('actions')">
                    <Link :href="route('emps.edit', emp.id)" class="mr-2">
                      <Button variant="outline" size="sm">編集</Button>
                    </Link>
                    <Button variant="destructive" size="sm" @click="deleteEmp(emp.id)">削除</Button>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>

            <div v-if="emps.links" class="mt-4 flex justify-center">
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <template v-for="(link, key) in emps.links" :key="key">
                  <Link
                    :href="link.url || ''"
                    v-html="link.label"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                    :class="{ 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': link.active, 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active }"
                    :disabled="!link.url"
                  />
                </template>
              </nav>
            </div>

          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
