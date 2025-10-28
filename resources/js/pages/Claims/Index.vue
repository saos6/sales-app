<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
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
import { reactive, ref, watch } from 'vue';
import { ChevronsUpDown, ArrowUp, ArrowDown } from 'lucide-vue-next';
import { pickBy } from 'lodash';

const props = defineProps<{
  claims: {
    data: Array<{
      id: string;
      type: string;
      voucher_no: string;
      cust_name: string;
      voucher_date: string;
      subtotal: number;
      tax: number;
      total: number;
    }>;
    links?: Array<{ url: string | null; label: string; active: boolean }>;
    meta?: { current_page: number; per_page: number; total: number };
  };
  filters: {
    search?: string;
    per_page?: string;
    sort_by?: string;
    sort_direction?: string;
  };
}>();

const params = reactive({
    search: props.filters.search,
    per_page: props.filters.per_page || '10',
    sort_by: props.filters.sort_by,
    sort_direction: props.filters.sort_direction,
});

watch(params, () => {
    router.get(route('claims.index'), pickBy(params), { preserveState: true, replace: true });
}, { deep: true });


const columns = reactive([
  { key: 'type', label: '区分', visible: true, sortable: true },
  { key: 'voucher_no', label: '伝票番号', visible: true, sortable: true },
  { key: 'cust_name', label: '顧客名', visible: true, sortable: true },
  { key: 'voucher_date', label: '日付', visible: true, sortable: true },
  { key: 'subtotal', label: '税抜金額', visible: true, sortable: true },
  { key: 'tax', label: '消費税', visible: true, sortable: true },
  { key: 'total', label: '税込金額', visible: true, sortable: true },
]);

const isVisible = (key: string) => columns.find(c => c.key === key)?.visible;

const sortBy = (key: string) => {
  if (!columns.find(c => c.key === key)?.sortable) return;

  if (params.sort_by === key) {
    params.sort_direction = params.sort_direction === 'asc' ? 'desc' : 'asc';
  } else {
    params.sort_by = key;
    params.sort_direction = 'asc';
  }
};

const sortIcon = (key: string) => {
  if (params.sort_by !== key) return ChevronsUpDown;
  if (params.sort_direction === 'asc') return ArrowUp;
  return ArrowDown;
};

</script>

<template>
  <Head title="請求検索" />

  <AppLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="mb-4">
                <div class="flex gap-2">
                  <Input v-model="params.search" placeholder="伝票番号、顧客名で検索..." class="max-w-xs" />
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
              <div class="flex gap-2">
                 <a :href="route('claims.export.invoices', pickBy(params))">
                  <Button variant="outline">請求書一括発行(pdf)</Button>
                </a>
                <a :href="route('claims.export.excel', pickBy(params))">
                  <Button variant="outline">Excelエクスポート</Button>
                </a>
                <a :href="route('claims.export.pdf', pickBy(params))">
                  <Button variant="outline">PDFエクスポート</Button>
                </a>
              </div>
              <div class="flex items-center gap-4">
                <Select v-model="params.per_page">
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

            <div v-if="claims.meta" class="text-sm text-gray-500 mb-4">
              {{ claims.meta.total }}件見つかりました。
            </div>

            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead v-if="isVisible('type')" @click="sortBy('type')" class="cursor-pointer">
                    <div class="flex items-center">
                      区分 <component :is="sortIcon('type')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('voucher_no')" @click="sortBy('voucher_no')" class="cursor-pointer">
                     <div class="flex items-center">
                      伝票番号 <component :is="sortIcon('voucher_no')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('cust_name')" @click="sortBy('cust_name')" class="cursor-pointer">
                     <div class="flex items-center">
                      顧客名 <component :is="sortIcon('cust_name')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('voucher_date')" @click="sortBy('voucher_date')" class="cursor-pointer">
                     <div class="flex items-center">
                      日付 <component :is="sortIcon('voucher_date')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('subtotal')" @click="sortBy('subtotal')" class="cursor-pointer text-right">
                     <div class="flex items-center justify-end">
                      税抜金額 <component :is="sortIcon('subtotal')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('tax')" @click="sortBy('tax')" class="cursor-pointer text-right">
                     <div class="flex items-center justify-end">
                      消費税 <component :is="sortIcon('tax')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('total')" @click="sortBy('total')" class="cursor-pointer text-right">
                     <div class="flex items-center justify-end">
                      税込金額 <component :is="sortIcon('total')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="claim in claims.data" :key="claim.id">
                  <TableCell v-if="isVisible('type')">{{ claim.type }}</TableCell>
                  <TableCell v-if="isVisible('voucher_no')">{{ claim.voucher_no }}</TableCell>
                  <TableCell v-if="isVisible('cust_name')">{{ claim.cust_name }}</TableCell>
                  <TableCell v-if="isVisible('voucher_date')">{{ claim.voucher_date }}</TableCell>
                  <TableCell v-if="isVisible('subtotal')" class="text-right">{{ claim.subtotal?.toLocaleString() }}</TableCell>
                  <TableCell v-if="isVisible('tax')" class="text-right">{{ claim.tax?.toLocaleString() }}</TableCell>
                  <TableCell v-if="isVisible('total')" class="text-right">{{ claim.total?.toLocaleString() }}</TableCell>
                </TableRow>
              </TableBody>
            </Table>

            <div v-if="claims.links" class="mt-4 flex justify-center">
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <template v-for="(link, key) in claims.links" :key="key">
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
