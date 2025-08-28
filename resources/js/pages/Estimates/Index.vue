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
  estimates: {
    data: Array<{
      id: string;
      estimate_no: string;
      estimate_date: string;
      valid_until: string;
      cust_name: string;
      subject: string;
      total: string;
      status: string;
    }>;
    links?: Array<{ url: string | null; label: string; active: boolean }>;
    meta?: { current_page: number; per_page: number; total: number };
  };
  filters: { search: string; per_page: string; sort: string; order: string };
}>();

const form = useForm({
  search: props.filters.search || '',
  per_page: props.filters.per_page || '10',
  sort: props.filters.sort || 'estimate_date',
  order: props.filters.order || 'desc',
});

const search = () => {
  form.get(route('estimates.index'), { preserveState: true });
};

watch(() => form.per_page, (newValue) => {
  router.get(route('estimates.index'), { ...route().params, per_page: newValue }, { preserveState: true });
});

const deleteEstimate = (id: string) => {
  if (confirm('本当に削除しますか？')) {
    router.delete(route('estimates.destroy', id));
  }
};

const columns = reactive([
  { key: 'estimate_no', label: '見積番号', visible: true, sortable: true },
  { key: 'estimate_date', label: '見積日', visible: true, sortable: true },
  { key: 'valid_until', label: '有効期限', visible: true, sortable: true },
  { key: 'cust_name', label: '顧客名', visible: true, sortable: true },
  { key: 'subject', label: '件名', visible: true, sortable: true },
  { key: 'total', label: '合計金額', visible: true, sortable: true },
  { key: 'status', label: 'ステータス', visible: true, sortable: true },
  { key: 'actions', label: '操作', visible: true, sortable: false },
]);

const isVisible = (key: string) => columns.find(c => c.key === key)?.visible;

const sortBy = (key: string) => {
  if (!columns.find(c => c.key === key)?.sortable) return;

  let newOrder = 'asc';
  if (form.sort === key && form.order === 'asc') {
    newOrder = 'desc';
  }

  form.sort = key;
  form.order = newOrder;
  form.get(route('estimates.index'), { preserveState: true });
};

const sortIcon = (key: string) => {
  if (form.sort !== key) return ChevronsUpDown;
  if (form.order === 'asc') return ArrowUp;
  return ArrowDown;
};

</script>

<template>
  <Head title="見積入力" />

  <AppLayout>
    <template #header>見積入力</template>

    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <Input v-model="form.search" placeholder="見積番号、件名、顧客名で検索..." class="w-64" @keyup.enter="search" />
          <Button @click="search">検索</Button>
          <Link :href="route('estimates.create')" class="ml-2">
            <Button>新規登録</Button>
          </Link>
          <Link :href="route('estimates.export', form)" class="ml-2">
            <Button variant="outline">Excelエクスポート</Button>
          </Link>
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

      <div v-if="estimates.meta" class="text-sm text-gray-500 mb-4">
        {{ estimates.meta.total }}件見つかりました。
      </div>

      <Table>
        <TableHeader>
          <TableRow>
            <TableHead v-if="isVisible('estimate_no')" @click="sortBy('estimate_no')" class="cursor-pointer">
              <div class="flex items-center">見積番号 <component :is="sortIcon('estimate_no')" class="ml-2 h-4 w-4" /></div>
            </TableHead>
            <TableHead v-if="isVisible('estimate_date')" @click="sortBy('estimate_date')" class="cursor-pointer">
              <div class="flex items-center">見積日 <component :is="sortIcon('estimate_date')" class="ml-2 h-4 w-4" /></div>
            </TableHead>
            <TableHead v-if="isVisible('valid_until')" @click="sortBy('valid_until')" class="cursor-pointer">
              <div class="flex items-center">有効期限 <component :is="sortIcon('valid_until')" class="ml-2 h-4 w-4" /></div>
            </TableHead>
            <TableHead v-if="isVisible('cust_name')" @click="sortBy('cust_name')" class="cursor-pointer">
              <div class="flex items-center">顧客名 <component :is="sortIcon('cust_name')" class="ml-2 h-4 w-4" /></div>
            </TableHead>
            <TableHead v-if="isVisible('subject')" @click="sortBy('subject')" class="cursor-pointer">
              <div class="flex items-center">件名 <component :is="sortIcon('subject')" class="ml-2 h-4 w-4" /></div>
            </TableHead>
            <TableHead v-if="isVisible('total')" @click="sortBy('total')" class="cursor-pointer">
              <div class="flex items-center">合計金額 <component :is="sortIcon('total')" class="ml-2 h-4 w-4" /></div>
            </TableHead>
            <TableHead v-if="isVisible('status')" @click="sortBy('status')" class="cursor-pointer">
              <div class="flex items-center">ステータス <component :is="sortIcon('status')" class="ml-2 h-4 w-4" /></div>
            </TableHead>
            <TableHead v-if="isVisible('actions')">操作</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="estimate in estimates.data" :key="estimate.id">
            <TableCell v-if="isVisible('estimate_no')">{{ estimate.estimate_no }}</TableCell>
            <TableCell v-if="isVisible('estimate_date')">{{ estimate.estimate_date }}</TableCell>
            <TableCell v-if="isVisible('valid_until')">{{ estimate.valid_until }}</TableCell>
            <TableCell v-if="isVisible('cust_name')">{{ estimate.cust_name }}</TableCell>
            <TableCell v-if="isVisible('subject')">{{ estimate.subject }}</TableCell>
            <TableCell v-if="isVisible('total')">{{ new Intl.NumberFormat('ja-JP', { style: 'currency', currency: 'JPY' }).format(parseFloat(estimate.total)) }}</TableCell>
            <TableCell v-if="isVisible('status')">{{ estimate.status }}</TableCell>
            <TableCell v-if="isVisible('actions')">
              <div class="flex gap-2">
                <Link :href="route('estimates.edit', estimate.id)"><Button size="sm" variant="outline">編集</Button></Link>
                <Button size="sm" variant="destructive" @click="deleteEstimate(estimate.id)">削除</Button>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>

      <div v-if="estimates.links" class="mt-4 flex justify-center">
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <template v-for="(link, key) in estimates.links" :key="key">
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
  </AppLayout>
</template>
