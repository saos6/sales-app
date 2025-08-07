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

defineProps<{
    customerTypes: any[];
    industryTypes: any[];
    customerRanks: any[];
    paymentTerms: any[];
    depts: any[];
    emps: any[];
}>();

const form = useForm({
    name: '',
    name_kana: '',
    department_name: '',
    contact_person_name: '',
    postal_code: '',
    prefecture: '',
    address_line: '',
    tel: '',
    fax: '',
    email: '',
    website_url: '',
    customer_type: null,
    industry_type: null,
    first_trade_date: null,
    customer_rank: null,
    payment_terms: null,
    depts_id: null,
    emps_id: null,
    remarks: '',
});

const submit = () => {
    form.post(route('custs.store'));
};
</script>

<template>
    <Head title="顧客 新規登録" />

    <AppLayout>
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>新しい顧客を登録します</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit">
                            <div class="grid gap-4">
                                <div>
                                    <Label for="name">名称</Label>
                                    <Input id="name" v-model="form.name" type="text" required />
                                    <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
                                </div>
                                <div>
                                    <Label for="name_kana">名称カナ</Label>
                                    <Input id="name_kana" v-model="form.name_kana" type="text" />
                                </div>
                                <div>
                                    <Label for="department_name">部署名</Label>
                                    <Input id="department_name" v-model="form.department_name" type="text" />
                                </div>
                                <div>
                                    <Label for="contact_person_name">担当者名</Label>
                                    <Input id="contact_person_name" v-model="form.contact_person_name" type="text" />
                                </div>
                                <div>
                                    <Label for="postal_code">郵便番号</Label>
                                    <Input id="postal_code" v-model="form.postal_code" type="text" />
                                </div>
                                <div>
                                    <Label for="prefecture">都道府県</Label>
                                    <Input id="prefecture" v-model="form.prefecture" type="text" />
                                </div>
                                <div>
                                    <Label for="address_line">市区町村以降の住所</Label>
                                    <Input id="address_line" v-model="form.address_line" type="text" />
                                </div>
                                <div>
                                    <Label for="tel">電話番号</Label>
                                    <Input id="tel" v-model="form.tel" type="text" />
                                </div>
                                <div>
                                    <Label for="fax">FAX番号</Label>
                                    <Input id="fax" v-model="form.fax" type="text" />
                                </div>
                                <div>
                                    <Label for="email">メールアドレス</Label>
                                    <Input id="email" v-model="form.email" type="email" />
                                </div>
                                <div>
                                    <Label for="website_url">ホームページURL</Label>
                                    <Input id="website_url" v-model="form.website_url" type="url" />
                                </div>
                                <div>
                                    <Label for="customer_type">顧客区分</Label>
                                    <Select v-model="form.customer_type">
                                        <SelectTrigger>
                                            <SelectValue placeholder="顧客区分を選択" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="item in customerTypes" :key="item.value" :value="item.value">
                                                {{ item.label }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <Label for="industry_type">業種</Label>
                                    <Select v-model="form.industry_type">
                                        <SelectTrigger>
                                            <SelectValue placeholder="業種を選択" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="item in industryTypes" :key="item.value" :value="item.value">
                                                {{ item.label }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <Label for="first_trade_date">取引開始日</Label>
                                    <Input id="first_trade_date" v-model="form.first_trade_date" type="date" />
                                </div>
                                <div>
                                    <Label for="customer_rank">顧客ランク</Label>
                                    <Select v-model="form.customer_rank">
                                        <SelectTrigger>
                                            <SelectValue placeholder="顧客ランクを選択" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="item in customerRanks" :key="item.value" :value="item.value">
                                                {{ item.label }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <Label for="payment_terms">支払条件</Label>
                                    <Select v-model="form.payment_terms">
                                        <SelectTrigger>
                                            <SelectValue placeholder="支払条件を選択" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="item in paymentTerms" :key="item.value" :value="item.value">
                                                {{ item.label }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <Label for="depts_id">所属</Label>
                                    <Select v-model="form.depts_id">
                                        <SelectTrigger>
                                            <SelectValue placeholder="所属を選択" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="dept in depts" :key="dept.id" :value="dept.id">
                                                {{ dept.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <Label for="emps_id">社員</Label>
                                    <Select v-model="form.emps_id">
                                        <SelectTrigger>
                                            <SelectValue placeholder="社員を選択" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="emp in emps" :key="emp.id" :value="emp.id">
                                                {{ emp.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <Label for="remarks">備考</Label>
                                    <Input id="remarks" v-model="form.remarks" type="text" />
                                </div>
                                <div class="flex justify-end">
                                    <Button :disabled="form.processing">登録</Button>
                                </div>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>