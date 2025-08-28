<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

const props = defineProps<{ item: any; categoryOptions: { value: number; label: string }[] }>();

const form = useForm({
  item_code: props.item.item_code,
  item_name: props.item.item_name,
  spec: props.item.spec,
  category_id: String(props.item.category_id ?? ''),
  unit: props.item.unit,
  standard_price: props.item.standard_price,
  cost_price: props.item.cost_price,
  tax_rate: props.item.tax_rate,
  currency: props.item.currency,
  maker: props.item.maker,
  jan_code: props.item.jan_code,
  stock_qty: props.item.stock_qty,
  reorder_point: props.item.reorder_point,
  lead_time: props.item.lead_time,
  status: props.item.status,
  remarks: props.item.remarks,
});

const submit = () => {
  form.put(route('items.update', props.item.id));
};
</script>

<template>
  <Head title="商品編集" />
  <AppLayout>
    <template #header>商品編集</template>

    <div class="space-y-4 max-w-3xl">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label>商品コード</label>
          <Input v-model="form.item_code" />
        </div>
        <div>
          <label>商品名</label>
          <Input v-model="form.item_name" />
        </div>
        <div>
          <label>仕様</label>
          <Input v-model="form.spec" />
        </div>
        <div>
          <label>カテゴリ</label>
          <Select v-model="form.category_id">
            <SelectTrigger>
              <SelectValue placeholder="選択してください" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem v-for="c in props.categoryOptions" :key="c.value" :value="String(c.value)">{{ c.label }}</SelectItem>
            </SelectContent>
          </Select>
        </div>
        <div>
          <label>単位</label>
          <Input v-model="form.unit" />
        </div>
        <div>
          <label>標準単価</label>
          <Input v-model="form.standard_price" type="number" />
        </div>
        <div>
          <label>原価</label>
          <Input v-model="form.cost_price" type="number" />
        </div>
        <div>
          <label>税率</label>
          <Input v-model="form.tax_rate" />
        </div>
        <div>
          <label>通貨</label>
          <Input v-model="form.currency" />
        </div>
        <div>
          <label>メーカー</label>
          <Input v-model="form.maker" />
        </div>
        <div>
          <label>JANコード</label>
          <Input v-model="form.jan_code" />
        </div>
        <div>
          <label>在庫数量</label>
          <Input v-model="form.stock_qty" type="number" />
        </div>
        <div>
          <label>発注点</label>
          <Input v-model="form.reorder_point" type="number" />
        </div>
        <div>
          <label>納期</label>
          <Input v-model="form.lead_time" type="number" />
        </div>
        <div>
          <label>状態</label>
          <Input v-model="form.status" />
        </div>
        <div class="col-span-2">
          <label>備考</label>
          <Input v-model="form.remarks" />
        </div>
      </div>
      <div class="flex gap-2">
        <Button @click="submit">更新</Button>
        <Link :href="route('items.index')"><Button variant="outline">戻る</Button></Link>
      </div>
    </div>
  </AppLayout>
</template>


