<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
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
import { computed } from 'vue';

const props = defineProps<{
    custs: any[];
    emps: any[];
    items: Array<{
        id: number;
        item_code: string;
        item_name: string;
        spec: string;
        unit: string;
        standard_price: number;
    }>;
}>();

const form = useForm({
    sales_date: new Date().toISOString().split('T')[0],
    posting_date: new Date().toISOString().split('T')[0],
    cust_id: null,
    emps_id: null,
    subject: '',
    details: [
        { item_id: null, item_code: '', item_name: '', quantity: 1, unit: '個', unit_price: 0 },
    ],
});

const subtotal = computed(() => {
    return form.details.reduce((acc, detail) => acc + detail.quantity * detail.unit_price, 0);
});

const tax = computed(() => {
    return subtotal.value * 0.1; // 10% tax
});

const total = computed(() => {
    return subtotal.value + tax.value;
});

const addDetail = () => {
    form.details.push({ item_id: null, item_code: '', item_name: '', quantity: 1, unit: '個', unit_price: 0 });
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
    detail.unit = selectedItem.unit;
    detail.unit_price = selectedItem.standard_price;
  }
};

const submit = () => {
    form.post(route('sales.store'));
};
</script>

<template>
    <Head title="売上 新規登録" />

    <AppLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>新しい売上を登録します</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <Label for="sales_date">売上日</Label>
                                    <Input id="sales_date" v-model="form.sales_date" type="date" required />
                                </div>
                                <div>
                                    <Label for="posting_date">計上日</Label>
                                    <Input id="posting_date" v-model="form.posting_date" type="date" required />
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
                                </div>
                                <div class="col-span-2">
                                    <Label for="subject">件名</Label>
                                    <Input id="subject" v-model="form.subject" type="text" required />
                                </div>
                            </div>

                            <div class="mt-8">
                                <h3 class="text-lg font-medium">明細</h3>
                                <table class="w-full mt-2">
                                    <thead>
                                        <tr>
                                            <th class="text-left py-2">品名</th>
                                            <th class="text-left py-2">商品コード</th>
                                            <th class="text-left py-2">数量</th>
                                            <th class="text-left py-2">単位</th>
                                            <th class="text-left py-2">単価</th>
                                            <th class="text-left py-2">金額</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(detail, index) in form.details" :key="index">
                                            <td>
                                                <Select v-model="detail.item_id" @update:modelValue="(value) => updateDetailFromItem(index, value)">
                                                    <SelectTrigger><SelectValue placeholder="商品を選択" /></SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem v-for="item in items" :key="item.id" :value="item.id">
                                                            {{ item.item_name }}
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </td>
                                            <td><Input v-model="detail.item_code" type="text" readonly /></td>
                                            <td><Input v-model="detail.quantity" type="number" /></td>
                                            <td><Input v-model="detail.unit" type="text" /></td>
                                            <td><Input v-model="detail.unit_price" type="number" /></td>
                                            <td>{{ (detail.quantity * detail.unit_price).toLocaleString() }}</td>
                                            <td>
                                                <Button type="button" variant="destructive" @click="removeDetail(index)">削除</Button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <Button type="button" class="mt-2" @click="addDetail">明細を追加</Button>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <div class="w-64">
                                    <div class="flex justify-between">
                                        <span>小計</span>
                                        <span>{{ subtotal.toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>消費税</span>
                                        <span>{{ tax.toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between font-bold text-lg">
                                        <span>合計</span>
                                        <span>{{ total.toLocaleString() }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <Button :disabled="form.processing">登録</Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>