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
    paymentCategories: Array<{ value: number; label: string }>;
}>();

const form = useForm({
    receipt_no: '',
    payment_date: new Date().toISOString().split('T')[0],
    cust_id: null,
    emps_id: null,
    remarks: '',
    details: [
        { payment_category: null, bank_info: '', amount: 0 },
    ],
});

const total_amount = computed(() => {
    return form.details.reduce((acc, detail) => acc + detail.amount, 0);
});

const addDetail = () => {
    form.details.push({ payment_category: null, bank_info: '', amount: 0 });
};

const removeDetail = (index: number) => {
    form.details.splice(index, 1);
};

const submit = () => {
    form.post(route('payments.store'));
};
</script>

<template>
    <Head title="入金 新規登録" />

    <AppLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>新しい入金を登録します</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <Label for="receipt_no">入金番号</Label>
                                    <Input id="receipt_no" v-model="form.receipt_no" type="text" required />
                                    <div v-if="form.errors.receipt_no" class="text-sm text-red-600 mt-1">{{ form.errors.receipt_no }}</div>
                                </div>
                                <div>
                                    <Label for="payment_date">入金日</Label>
                                    <Input id="payment_date" v-model="form.payment_date" type="date" required />
                                    <div v-if="form.errors.payment_date" class="text-sm text-red-600 mt-1">{{ form.errors.payment_date }}</div>
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
                                    <Label for="remarks">備考</Label>
                                    <Input id="remarks" v-model="form.remarks" type="text" />
                                </div>
                            </div>

                            <div class="mt-8">
                                <h3 class="text-lg font-medium">明細</h3>
                                <table class="w-full mt-2">
                                    <thead>
                                        <tr>
                                            <th class="text-left py-2">入金区分</th>
                                            <th class="text-left py-2">銀行情報</th>
                                            <th class="text-left py-2">金額</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(detail, index) in form.details" :key="index">
                                            <td>
                                                <Select v-model="detail.payment_category">
                                                    <SelectTrigger><SelectValue placeholder="入金区分を選択" /></SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem v-for="category in paymentCategories" :key="category.value" :value="category.value">
                                                            {{ category.label }}
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </td>
                                            <td><Input v-model="detail.bank_info" type="text" /></td>
                                            <td><Input v-model="detail.amount" type="number" /></td>
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
                                    <div class="flex justify-between font-bold text-lg">
                                        <span>合計金額</span>
                                        <span>{{ total_amount.toLocaleString() }}</span>
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
