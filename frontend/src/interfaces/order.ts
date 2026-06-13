import type { ID, KeywordPaginationQuery } from "./common";

export interface OrderItem {
  id: ID;
  order_id: ID;
  product_id: ID | null;
  product_name: string;
  qty: number | string;
  unit_price: number | string;
  subtotal: number | string;
  created_at?: string;
  updated_at?: string;
}

export interface Order {
  id: ID;
  user_id: ID | null;
  user_full_name?: string | null;
  order_number: string;
  customer_name: string;
  customer_email: string | null;
  customer_phone: string | null;
  channel: string;
  status: "Waiting Payment" | "Processing" | "Packed" | "Shipped" | "Completed" | "Cancelled";
  payment_status: "Unpaid" | "Paid" | "Refunded";
  total_items: number | string;
  total_amount: number | string;
  notes: string | null;
  items?: OrderItem[];
  created_at?: string;
  updated_at?: string;
}

export interface OrderListQuery extends KeywordPaginationQuery {
  status?: "" | "Waiting Payment" | "Processing" | "Packed" | "Shipped" | "Completed" | "Cancelled";
}

export interface OrderCreateItemPayload {
  product_id?: ID | null;
  product_name?: string;
  qty: number | string;
  unit_price?: number | string;
}

export interface OrderCreatePayload {
  user_id?: ID | null;
  customer_name: string;
  customer_email?: string | null;
  customer_phone?: string | null;
  channel: string;
  status: "Waiting Payment" | "Processing" | "Packed" | "Shipped" | "Completed" | "Cancelled";
  payment_status: "Unpaid" | "Paid" | "Refunded";
  notes?: string | null;
  items: OrderCreateItemPayload[];
}

export interface OrderUpdatePayload extends OrderCreatePayload {}
