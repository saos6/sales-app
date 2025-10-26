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
  payments: {
    data: Array<{
      id: string;
      payment_no: string;
      payment_date: string;
      cust_name: string;
      remarks: string;
      total_amount: number;
      status: string;
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
  form.get(route('payments.index'), { preserveState: true });
};

watch(() => form.per_page, (newValue) => {
  router.get(route('payments.index'), { ...route().params, per_page: newValue }, { preserveState: true });
});

const deletePayment = (id: string) => {
  if (confirm('本当に削除しますか？')) {
    router.delete(route('payments.destroy', id), {
      onSuccess: () => {
        // Success notification can be handled here
      },
    });
  }
};

const columns = reactive([
  { key: 'payment_no', label: '入金番号', visible: true, sortable: true },
  { key: 'payment_date', label: '入金日', visible: true, sortable: true },
  { key: 'cust_name', label: '顧客名', visible: true, sortable: true },
  { key: 'remarks', label: '備考', visible: true, sortable: true },
  { key: 'total_amount', label: '合計金額', visible: true, sortable: true },
  { key: 'status', label: 'ステータス', visible: true, sortable: true },
  { key: 'actions', label: '操作', visible: true, sortable: false },
]);

const isVisible = (key: string) => columns.find(c => c.key === key)?.visible;

const sortBy = (key: string) => {
  if (!columns.find(c => c.key === key)?.sortable) return;

  let newOrder = 'asc';
  if (props.sort === key && props.order === 'asc') {
    newOrder = 'desc';
  }

  router.get(route('payments.index'), { ...route().params, sort: key, order: newOrder }, { preserveState: true });
};

const sortIcon = (key: string) => {
  if (props.sort !== key) return ChevronsUpDown;
  if (props.order === 'asc') return ArrowUp;
  return ArrowDown;
};

</script>

<template>
  <Head title="入金管理" />

  <AppLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="mb-4">
              <form @submit.prevent="search">
                <div class="flex gap-2">
                  <Input v-model="form.search" placeholder="入金番号、備考、顧客名で検索..." class="max-w-xs" />
                  <Button type="submit">検索</Button>
                </div>
              </form>
            </div>

            <div class="flex justify-between items-center mb-4">
              <div class="flex gap-2">
                <Link :href="route('payments.create')">
                  <Button>新規登録</Button>
                </Link>
                <a :href="route('payments.export', { search: form.search })">
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

            <div v-if="payments.meta" class="text-sm text-gray-500 mb-4">
              {{ payments.meta.total }}件見つかりました。
            </div>

            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead v-if="isVisible('payment_no')" @click="sortBy('payment_no')" class="cursor-pointer">
                    <div class="flex items-center">
                      入金番号 <component :is="sortIcon('payment_no')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('payment_date')" @click="sortBy('payment_date')" class="cursor-pointer">
                     <div class="flex items-center">
                      入金日 <component :is="sortIcon('payment_date')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('cust_name')" @click="sortBy('cust_name')" class="cursor-pointer">
                     <div class="flex items-center">
                      顧客名 <component :is="sortIcon('cust_name')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('remarks')" @click="sortBy('remarks')" class="cursor-pointer">
                     <div class="flex items-center">
                      備考 <component :is="sortIcon('remarks')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('total_amount')" @click="sortBy('total_amount')" class="cursor-pointer">
                     <div class="flex items-center">
                      合計金額 <component :is="sortIcon('total_amount')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('status')" @click="sortBy('status')" class="cursor-pointer">
                     <div class="flex items-center">
                      ステータス <component :is="sortIcon('status')" class="ml-2 h-4 w-4" />
                    </div>
                  </TableHead>
                  <TableHead v-if="isVisible('actions')">操作</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="payment in payments.data" :key="payment.id">
                  <TableCell v-if="isVisible('payment_no')">{{ payment.payment_no }}</TableCell>
                  <TableCell v-if="isVisible('payment_date')">{{ payment.payment_date }}</TableCell>
                  <TableCell v-if="isVisible('cust_name')">{{ payment.cust_name }}</TableCell>
                  <TableCell v-if="isVisible('remarks')">{{ payment.remarks }}</TableCell>
                  <TableCell v-if="isVisible('total_amount')">{{ payment.total_amount }}</TableCell>
                  <TableCell v-if="isVisible('status')">{{ payment.status }}</TableCell>
                  <TableCell v-if="isVisible('actions')">
                    <Link :href="route('payments.edit', payment.id)" class="mr-2">
                      <Button variant="outline" size="sm">編集</Button>
                    </Link>
                    <Button variant="destructive" size="sm" @click="deletePayment(payment.id)">削除</Button>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>

            <div v-if="payments.links" class="mt-4 flex justify-center">
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <template v-for="(link, key) in payments.links" :key="key">
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
