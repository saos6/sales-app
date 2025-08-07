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
  custs: {
    data: Array<{
      id: number;
      name: string;
      contact_person_name: string;
      tel: string;
      email: string;
    }>;
    links?: Array<{ url: string | null; label: string; active: boolean }>;
    meta?: { current_page: number; per_page: number; total: number };
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
  form.get(route('custs.index'), { preserveState: true });
};

watch(() => form.per_page, (newValue) => {
  router.get(route('custs.index'), { ...route().params, per_page: newValue }, { preserveState: true });
});

const deleteCust = (id: number) => {
  if (confirm('本当に削除しますか？')) {
    router.delete(route('custs.destroy', id), {
      onSuccess: () => {
        // 必要に応じて、ここで通知を表示したり、
        // 親コンポーネントにイベントをemitしたりできます。
      },
    });
  }
};

const columns = reactive([
  { key: 'id', label: 'ID', visible: true, sortable: true },
  { key: 'name', label: '名称', visible: true, sortable: true },
  { key: 'contact_person_name', label: '担当者名', visible: true, sortable: true },
  { key: 'tel', label: '電話番号', visible: true, sortable: true },
  { key: 'email', label: 'メールアドレス', visible: true, sortable: true },
  { key: 'actions', label: '操作', visible: true, sortable: false },
]);

const isVisible = (key: string) => columns.find(c => c.key === key)?.visible;

const sortBy = (key: string) => {
  if (!columns.find(c => c.key === key)?.sortable) return;

  let newOrder = 'asc';
  if (props.sort === key && props.order === 'asc') {
    newOrder = 'desc';
  }

  router.get(route('custs.index'), { ...route().params, sort: key, order: newOrder }, { preserveState: true });
};

const sortIcon = (key: string) => {
  if (props.sort !== key) return ChevronsUpDown;
  if (props.order === 'asc') return ArrowUp;
  return ArrowDown;
};

</script>

<template>
  <Head title="顧客マスタ" />

  <AppLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="mb-4">
              <form @submit.prevent="search">
                <div class="flex gap-2">
                  <Input v-model="form.search" placeholder="名称、かな、電話番号、Emailで検索..." class="max-w-xs" />
                  <Button type="submit">検索</Button>
                </div>
              </form>
            </div>

            <div class="flex justify-between items-center mb-4">
              <div class="flex gap-2">
                <Link :href="route('custs.create')">
                  <Button>新規登録</Button>
                </Link>
                <a :href="route('custs.export', { search: form.search })">
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

            <div v-if="custs.meta" class="text-sm text-gray-500 mb-4">
              {{ custs.meta.total }}件見つかりました。
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
                      名称 <component :is="sortIcon('name')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('contact_person_name')" @click="sortBy('contact_person_name')" class="cursor-pointer">
                     <div class="flex items-center">
                      担当者名 <component :is="sortIcon('contact_person_name')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('tel')" @click="sortBy('tel')" class="cursor-pointer">
                     <div class="flex items-center">
                      電話番号 <component :is="sortIcon('tel')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('email')" @click="sortBy('email')" class="cursor-pointer">
                     <div class="flex items-center">
                      メールアドレス <component :is="sortIcon('email')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('actions')">操作</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="cust in custs.data" :key="cust.id">
                  <TableCell v-if="isVisible('id')">{{ cust.id }}</TableCell>
                  <TableCell v-if="isVisible('name')">{{ cust.name }}</TableCell>
                  <TableCell v-if="isVisible('contact_person_name')">{{ cust.contact_person_name }}</TableCell>
                  <TableCell v-if="isVisible('tel')">{{ cust.tel }}</TableCell>
                  <TableCell v-if="isVisible('email')">{{ cust.email }}</TableCell>
                  <TableCell v-if="isVisible('actions')">
                    <Link :href="route('custs.edit', cust.id)" class="mr-2">
                      <Button variant="outline" size="sm">編集</Button>
                    </Link>
                    <Button variant="destructive" size="sm" @click="deleteCust(cust.id)">削除</Button>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>

            <div v-if="custs.links" class="mt-4 flex justify-center">
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <template v-for="(link, key) in custs.links" :key="key">
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
