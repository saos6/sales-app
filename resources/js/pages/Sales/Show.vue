<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

interface SalesM {
    id: number;
    line_no: number;
    item_code: string;
    item_name: string;
    spec: string;
    quantity: number;
    unit: string;
    unit_price: number;
    amount: number;
    tax_rate: number;
    tax_amount: number;
    delivery_qty: number;
    remarks: string;
}

interface Customer {
    id: number;
    cust_name: string;
}

interface Employee {
    id: number;
    emp_name: string;
}

interface SalesH {
    id: number;
    sales_no: string;
    sales_date: string;
    posting_date: string;
    invoice_id: number | null;
    estimate_id: number | null;
    cust_id: number;
    cust_name: string;
    cust_department: string;
    cust_person: string;
    emps_id: number;
    subject: string;
    subtotal: number;
    tax: number;
    total: number;
    payment_status: string;
    payment_date: string | null;
    receipt_no: string;
    remarks: string;
    status: string;
    sales_m: SalesM[];
    customer: Customer;
    employee: Employee;
}

interface SalesShowProps {
    sales: SalesH;
}

const props = defineProps<SalesShowProps>();
</script>

<template>
    <AppLayout title="売上詳細">
        <Head title="売上詳細" />
        <template #header>
            <Heading>売上詳細</Heading>
        </template>

        <div class="p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Card class="mb-6">
                    <CardHeader>
                        <CardTitle>売上ヘッダ情報</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><strong>売上番号:</strong> {{ sales.sales_no }}</div>
                            <div><strong>売上日:</strong> {{ sales.sales_date }}</div>
                            <div><strong>計上日:</strong> {{ sales.posting_date }}</div>
                            <div><strong>顧客名:</strong> {{ sales.cust_name }}</div>
                            <div><strong>部署:</strong> {{ sales.cust_department }}</div>
                            <div><strong>担当者:</strong> {{ sales.cust_person }}</div>
                            <div><strong>社内担当:</strong> {{ sales.employee.emp_name }}</div>
                            <div><strong>件名／案件名:</strong> {{ sales.subject }}</div>
                            <div><strong>入金状況:</strong> {{ sales.payment_status }}</div>
                            <div><strong>ステータス:</strong> {{ sales.status }}</div>
                            <div><strong>備考:</strong> {{ sales.remarks }}</div>
                            <div><strong>小計:</strong> {{ sales.subtotal.toLocaleString() }}</div>
                            <div><strong>消費税額:</strong> {{ sales.tax.toLocaleString() }}</div>
                            <div><strong>合計金額:</strong> {{ sales.total.toLocaleString() }}</div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>明細情報</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>#</TableHead>
                                        <TableHead>品目コード</TableHead>
                                        <TableHead>品名・サービス名</TableHead>
                                        <TableHead>仕様・型番</TableHead>
                                        <TableHead class="text-right">数量</TableHead>
                                        <TableHead>単位</TableHead>
                                        <TableHead class="text-right">単価</TableHead>
                                        <TableHead class="text-right">金額</TableHead>
                                        <TableHead class="text-right">税率</TableHead>
                                        <TableHead class="text-right">明細税額</TableHead>
                                        <TableHead>備考</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="item in sales.sales_m" :key="item.id">
                                        <TableCell>{{ item.line_no }}</TableCell>
                                        <TableCell>{{ item.item_code }}</TableCell>
                                        <TableCell>{{ item.item_name }}</TableCell>
                                        <TableCell>{{ item.spec }}</TableCell>
                                        <TableCell class="text-right">{{ item.quantity }}</TableCell>
                                        <TableCell>{{ item.unit }}</TableCell>
                                        <TableCell class="text-right">{{ item.unit_price.toLocaleString() }}</TableCell>
                                        <TableCell class="text-right">{{ item.amount.toLocaleString() }}</TableCell>
                                        <TableCell class="text-right">{{ item.tax_rate }}</TableCell>
                                        <TableCell class="text-right">{{ item.tax_amount.toLocaleString() }}</TableCell>
                                        <TableCell>{{ item.remarks }}</TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end mt-6">
                    <Link :href="route('sales.edit', sales.id)" class="mr-2">
                        <Button>編集</Button>
                    </Link>
                    <Link :href="route('sales.index')">
                        <Button variant="outline">一覧に戻る</Button>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
