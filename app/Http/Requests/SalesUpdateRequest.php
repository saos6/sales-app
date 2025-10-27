<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sales_no' => ['required', 'string', 'max:20', Rule::unique('sales_h')->ignore($this->sales_h)],
            'sales_date' => ['required', 'date'],
            'posting_date' => ['required', 'date'],
            'invoice_id' => ['nullable', 'integer'],
            'estimate_id' => ['nullable', 'integer'],
            'cust_id' => ['required', 'integer', 'exists:custs,id'],
            'cust_name' => ['required', 'string', 'max:100'],
            'cust_department' => ['nullable', 'string', 'max:100'],
            'cust_person' => ['nullable', 'string', 'max:100'],
            'emps_id' => ['required', 'integer', 'exists:emps,id'],
            'subject' => ['nullable', 'string', 'max:200'],
            'subtotal' => ['required', 'numeric', 'decimal:0,2'],
            'tax' => ['required', 'numeric', 'decimal:0,2'],
            'total' => ['required', 'numeric', 'decimal:0,2'],
            'payment_status' => ['required', 'string', 'max:20'],
            'payment_date' => ['nullable', 'date'],
            'receipt_no' => ['nullable', 'string', 'max:50'],
            'remarks' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:20'],
            'sales_m' => ['required', 'array', 'min:1'],
            'sales_m.*.id' => ['nullable', 'integer', 'exists:sales_m,id'], // For existing sales_m records
            'sales_m.*.line_no' => ['required', 'integer'],
            'sales_m.*.item_code' => ['required', 'string', 'max:50', 'exists:items,item_code'],
            'sales_m.*.item_name' => ['required', 'string', 'max:200'],
            'sales_m.*.spec' => ['nullable', 'string', 'max:200'],
            'sales_m.*.quantity' => ['required', 'numeric', 'decimal:0,4'],
            'sales_m.*.unit' => ['nullable', 'string', 'max:20'],
            'sales_m.*.unit_price' => ['required', 'numeric', 'decimal:0,4'],
            'sales_m.*.amount' => ['required', 'numeric', 'decimal:0,2'],
            'sales_m.*.tax_rate' => ['required', 'numeric', 'decimal:0,4'],
            'sales_m.*.tax_amount' => ['nullable', 'numeric', 'decimal:0,2'],
            'sales_m.*.delivery_qty' => ['nullable', 'numeric', 'decimal:0,4'],
            'sales_m.*.remarks' => ['nullable', 'string', 'max:255'],
            'sales_m_deleted_ids' => ['nullable', 'array'], // To handle deleted sales_m records
            'sales_m_deleted_ids.*' => ['integer', 'exists:sales_m,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'sales_no.required' => '売上番号は必須です。',
            'sales_no.unique' => 'この売上番号は既に存在します。',
            'sales_date.required' => '売上日は必須です。',
            'posting_date.required' => '計上日は必須です。',
            'cust_id.required' => '顧客は必須です。',
            'cust_id.exists' => '選択された顧客は無効です。',
            'cust_name.required' => '顧客名は必須です。',
            'emps_id.required' => '社内担当は必須です。',
            'emps_id.exists' => '選択された社内担当は無効です。',
            'subtotal.required' => '小計は必須です。',
            'tax.required' => '消費税額は必須です。',
            'total.required' => '合計金額は必須です。',
            'payment_status.required' => '入金状況は必須です。',
            'status.required' => 'ステータスは必須です。',
            'sales_m.required' => '明細は必須です。',
            'sales_m.min' => '明細は少なくとも1つ必要です。',
            'sales_m.*.line_no.required' => '行番号は必須です。',
            'sales_m.*.item_code.required' => '品目コードは必須です。',
            'sales_m.*.item_code.exists' => '選択された品目コードは無効です。',
            'sales_m.*.item_name.required' => '品名・サービス名は必須です。',
            'sales_m.*.quantity.required' => '数量は必須です。',
            'sales_m.*.unit_price.required' => '単価は必須です。',
            'sales_m.*.amount.required' => '金額は必須です。',
            'sales_m.*.tax_rate.required' => '税率は必須です。',
        ];
    }
}
