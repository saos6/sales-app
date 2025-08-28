<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
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
import { Textarea } from '@/components/ui/textarea';
import { PlusCircle, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
  estimate: {
    id: number;
    estimate_no: string;
    estimate_date: string;
    valid_until: string;
    cust_id: number;
    emps_id: number;
    subject: string;
    currency: string;
    status: string;
    remarks: string;
    details: Array<{
      id: number;
      item_id: number;
      item_code: string;
      item_name: string;
      spec: string;
      quantity: number;
      unit: string;
      unit_price: number;
      tax_rate: string;
      remarks: string;
    }>;
  };
  custs: Array<{ id: number; name: string }>;
  emps: Array<{ id: number; name: string }>;
  items: Array<{
    id: number;
    item_code: string;
    item_name: string;
    spec: string;
    unit: string;
    standard_price: number;
  }>;
  errors: Object;
}>();

const form = useForm({
  _method: 'PUT',
  estimate_no: props.estimate.estimate_no,
  estimate_date: props.estimate.estimate_date,
  valid_until: props.estimate.valid_until,
  cust_id: props.estimate.cust_id,
  emps_id: props.estimate.emps_id,
  subject: props.estimate.subject,
  currency: props.estimate.currency,
  status: props.estimate.status,
  remarks: props.estimate.remarks,
  details: props.estimate.details.map(d => ({ ...d, item_id: props.items.find(item => item.item_code === d.item_code)?.id || null })),
});

const addDetail = () => {
  form.details.push({
    item_id: null,
    item_code: '',
    item_name: '',
    spec: '',
    quantity: 1,
    unit: '式',
    unit_price: 0,
    tax_rate: '標準',
    remarks: '',
  });
};

const removeDetail = (index: number) => {
  form.details.splice(index, 1);
};

const updateDetailFromItem = (index: number, itemId: number) => {
  const selectedItem = props.items.find(item => item.id === itemId);
  if (selectedItem) {
    const detail = form.details[index];
    detail.item_code = selectedItem.item_code;
    detail.item_name = selectedItem.item_name;
    detail.spec = selectedItem.spec;
    detail.unit = selectedItem.unit;
    detail.unit_price = selectedItem.standard_price;
  }
};

const submit = () => {
  form.post(route('estimates.update', props.estimate.id));
};

const subtotal = computed(() => {
  return form.details.reduce((acc, detail) => {
    return acc + (detail.quantity * detail.unit_price);
  }, 0);
});

const tax = computed(() => {
  return form.details.reduce((acc, detail) => {
    const amount = detail.quantity * detail.unit_price;
    let taxRate = 0;
    if (detail.tax_rate === '標準') {
      taxRate = 0.10;
    } else if (detail.tax_rate === '軽減') {
      taxRate = 0.08;
    }
    return acc + (amount * taxRate);
  }, 0);
});

const total = computed(() => {
  return subtotal.value + tax.value;
});

</script>

<template>
  <Head title="見積 編集" />

  <AppLayout>
    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <Card>
          <CardHeader>
            <CardTitle>見積を編集します</CardTitle>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label for="estimate_no">見積番号</Label>
                  <Input id="estimate_no" v-model="form.estimate_no" type="text" required />
                  <div v-if="errors.estimate_no" class="text-sm text-red-600 mt-1">{{ errors.estimate_no }}</div>
                </div>
                <div>
                  <Label for="subject">件名</Label>
                  <Input id="subject" v-model="form.subject" type="text" required />
                   <div v-if="errors.subject" class="text-sm text-red-600 mt-1">{{ errors.subject }}</div>
                </div>
                <div>
                  <Label for="estimate_date">見積日</Label>
                  <Input id="estimate_date" v-model="form.estimate_date" type="date" required />
                   <div v-if="errors.estimate_date" class="text-sm text-red-600 mt-1">{{ errors.estimate_date }}</div>
                </div>
                <div>
                  <Label for="valid_until">有効期限</Label>
                  <Input id="valid_until" v-model="form.valid_until" type="date" />
                   <div v-if="errors.valid_until" class="text-sm text-red-600 mt-1">{{ errors.valid_until }}</div>
                </div>
                <div>
                  <Label for="cust_id">顧客</Label>
                  <Select v-model="form.cust_id">
                    <SelectTrigger>
                      <SelectValue placeholder="顧客を選択" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem v-for="cust in custs" :key="cust.id" :value="cust.id">
                        {{ cust.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                   <div v-if="errors.cust_id" class="text-sm text-red-600 mt-1">{{ errors.cust_id }}</div>
                </div>
                <div>
                  <Label for="emps_id">担当者</Label>
                  <Select v-model="form.emps_id">
                    <SelectTrigger>
                      <SelectValue placeholder="担当者を選択" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem v-for="emp in emps" :key="emp.id" :value="emp.id">
                        {{ emp.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                   <div v-if="errors.emps_id" class="text-sm text-red-600 mt-1">{{ errors.emps_id }}</div>
                </div>
                 <div>
                  <Label for="status">ステータス</Label>
                  <Select v-model="form.status">
                    <SelectTrigger>
                      <SelectValue placeholder="ステータスを選択" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="作成中">作成中</SelectItem>
                      <SelectItem value="提出済">提出済</SelectItem>
                      <SelectItem value="受注">受注</SelectItem>
                      <SelectItem value="失注">失注</SelectItem>
                    </SelectContent>
                  </Select>
                   <div v-if="errors.status" class="text-sm text-red-600 mt-1">{{ errors.status }}</div>
                </div>
                <div>
                  <Label for="currency">通貨</Label>
                  <Input id="currency" v-model="form.currency" type="text" required />
                   <div v-if="errors.currency" class="text-sm text-red-600 mt-1">{{ errors.currency }}</div>
                </div>
                <div class="md:col-span-2">
                  <Label for="remarks">備考</Label>
                  <Textarea id="remarks" v-model="form.remarks" />
                </div>
              </div>

              <div class="mt-8">
                <h3 class="text-lg font-medium">明細</h3>
                <div class="mt-4 space-y-4">
                  <div v-for="(detail, index) in form.details" :key="index" class="p-4 border rounded-md">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                      <div class="md:col-span-2">
                        <Label :for="`item_id_${index}`">品名</Label>
                        <Select v-model="detail.item_id" @update:modelValue="(value) => updateDetailFromItem(index, value)">
                            <SelectTrigger><SelectValue placeholder="商品を選択" /></SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="item in items" :key="item.id" :value="item.id">
                                    {{ item.item_name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                      </div>
                      <div class="md:col-span-2">
                        <Label :for="`item_code_${index}`">商品コード</Label>
                        <Input :id="`item_code_${index}`" v-model="detail.item_code" type="text" readonly />
                      </div>
                      <div>
                        <Label :for="`quantity_${index}`">数量</Label>
                        <Input :id="`quantity_${index}`" v-model="detail.quantity" type="number" required />
                      </div>
                      <div>
                        <Label :for="`unit_price_${index}`">単価</Label>
                        <Input :id="`unit_price_${index}`" v-model="detail.unit_price" type="number" required />
                      </div>
                      <div class="md:col-span-2">
                        <Label :for="`amount_${index}`">金額</Label>
                        <Input :id="`amount_${index}`" :value="(detail.quantity * detail.unit_price).toLocaleString()" type="text" readonly />
                      </div>
                      <div>
                        <Label :for="`unit_${index}`">単位</Label>
                        <Input :id="`unit_${index}`" v-model="detail.unit" type="text" />
                      </div>
                       <div>
                        <Label :for="`tax_rate_${index}`">税率</Label>
                        <Select v-model="detail.tax_rate">
                            <SelectTrigger><SelectValue placeholder="税率を選択" /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="標準">標準 (10%)</SelectItem>
                                <SelectItem value="軽減">軽減 (8%)</SelectItem>
                                <SelectItem value="対象外">対象外</SelectItem>
                            </SelectContent>
                        </Select>
                      </div>
                      <div class="md:col-span-1">
                        <Label :for="`spec_${index}`">仕様</Label>
                        <Input :id="`spec_${index}`" v-model="detail.spec" type="text" />
                      </div>
                      <div class="flex items-end">
                        <Button type="button" variant="destructive" @click="removeDetail(index)">
                          <Trash2 class="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  </div>
                </div>
                <Button type="button" class="mt-4" @click="addDetail">
                  <PlusCircle class="h-4 w-4 mr-2" />
                  明細を追加
                </Button>
              </div>

              <div class="mt-8 flex justify-end">
                <div class="w-full max-w-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="font-medium">小計</span>
                        <span>{{ subtotal.toLocaleString() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">消費税</span>
                        <span>{{ tax.toLocaleString() }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t font-bold">
                        <span class="text-lg">合計</span>
                        <span class="text-lg">{{ total.toLocaleString() }}</span>
                    </div>
                </div>
              </div>

              <div class="flex justify-end mt-8">
                <Button :disabled="form.processing">更新</Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>