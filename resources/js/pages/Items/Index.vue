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
  items: {
    data: Array<{
      id: number;
      item_code: string;
      item_name: string;
      spec: string | null;
      unit: string;
      standard_price: string | number | null;
      cost_price: string | number | null;
      maker: string | null;
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
  form.get(route('items.index'), { preserveState: true });
};

watch(() => form.per_page, (newValue) => {
  router.get(route('items.index'), { ...route().params, per_page: newValue }, { preserveState: true });
});

const deleteItem = (id: number) => {
  if (confirm('本当に削除しますか？')) {
    router.delete(route('items.destroy', id));
  }
};

const columns = reactive([
  { key: 'item_code', label: '商品コード', visible: true, sortable: true },
  { key: 'item_name', label: '商品名', visible: true, sortable: true },
  { key: 'spec', label: '仕様', visible: true, sortable: true },
  { key: 'unit', label: '単位', visible: true, sortable: true },
  { key: 'standard_price', label: '標準単価', visible: true, sortable: true },
  { key: 'maker', label: 'メーカー', visible: true, sortable: true },
  { key: 'actions', label: '操作', visible: true, sortable: false },
]);

const isVisible = (key: string) => columns.find(c => c.key === key)?.visible;

const sortBy = (key: string) => {
  if (!columns.find(c => c.key === key)?.sortable) return;

  let newOrder = 'asc';
  if (props.sort === key && props.order === 'asc') {
    newOrder = 'desc';
  }

  router.get(route('items.index'), { ...route().params, sort: key, order: newOrder }, { preserveState: true });
};

const sortIcon = (key: string) => {
  if (props.sort !== key) return ChevronsUpDown;
  if (props.order === 'asc') return ArrowUp;
  return ArrowDown;
};
</script>

<template>
  <Head title="商品マスタ" />
  <AppLayout>
    <template #header>商品マスタ</template>

    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <Input v-model="form.search" placeholder="商品名・コードを検索" class="w-64" @keyup.enter="search" />
          <Button @click="search">検索</Button>
          <Link :href="route('items.create')" class="mr-2">
            <Button>新規登録</Button>
          </Link>
          <Link :href="route('items.export', form)" class="ml-2">
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

      <div v-if="items.meta" class="text-sm text-gray-500 mb-4">
        {{ items.meta.total }}件見つかりました。
      </div>

      <Table>
        <TableHeader>
          <TableRow>
            <TableHead v-if="isVisible('item_code')" @click="sortBy('item_code')" class="cursor-pointer">
              <div class="flex items-center">商品コード <component :is="sortIcon('item_code')" class="ml-2 h-4 w-4" /></div>
            </TableHead>
            <TableHead v-if="isVisible('item_name')" @click="sortBy('item_name')" class="cursor-pointer">
              <div class="flex items-center">商品名 <component :is="sortIcon('item_name')" class="ml-2 h-4 w-4" /></div>
            </TableHead>
            <TableHead v-if="isVisible('spec')" @click="sortBy('spec')" class="cursor-pointer">
              <div class="flex items-center">仕様 <component :is="sortIcon('spec')" class="ml-2 h-4 w-4" /></div>
            </TableHead>
            <TableHead v-if="isVisible('unit')" @click="sortBy('unit')" class="cursor-pointer">
              <div class="flex items-center">単位 <component :is="sortIcon('unit')" class="ml-2 h-4 w-4" /></div>
            </TableHead>
            <TableHead v-if="isVisible('standard_price')" @click="sortBy('standard_price')" class="cursor-pointer">
              <div class="flex items-center">標準単価 <component :is="sortIcon('standard_price')" class="ml-2 h-4 w-4" /></div>
            </TableHead>
            <TableHead v-if="isVisible('maker')" @click="sortBy('maker')" class="cursor-pointer">
              <div class="flex items-center">メーカー <component :is="sortIcon('maker')" class="ml-2 h-4 w-4" /></div>
            </TableHead>
            <TableHead v-if="isVisible('actions')">操作</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="item in items.data" :key="item.id">
            <TableCell v-if="isVisible('item_code')">{{ item.item_code }}</TableCell>
            <TableCell v-if="isVisible('item_name')">{{ item.item_name }}</TableCell>
            <TableCell v-if="isVisible('spec')">{{ item.spec }}</TableCell>
            <TableCell v-if="isVisible('unit')">{{ item.unit }}</TableCell>
            <TableCell v-if="isVisible('standard_price')">{{ item.standard_price }}</TableCell>
            <TableCell v-if="isVisible('maker')">{{ item.maker }}</TableCell>
            <TableCell v-if="isVisible('actions')">
              <div class="flex gap-2">
                <Link :href="route('items.edit', item.id)"><Button size="sm" variant="outline">編集</Button></Link>
                <Button size="sm" variant="destructive" @click="deleteItem(item.id)">削除</Button>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>

      <div v-if="items.links" class="mt-4 flex justify-center">
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <template v-for="(link, key) in items.links" :key="key">
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


