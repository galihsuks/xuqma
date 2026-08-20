import type { ID } from "./common";

export interface CartItem {
  id: ID;
  cart_id: ID;
  product_id: ID;
  product_name: string | null;
  category_name: string | null;
  price: number | string;
  qty: number | string;
  subtotal: number | string;
  created_at?: string;
  updated_at?: string;
}

export interface Cart {
  id: ID | null;
  user_id: ID;
  total_qty: number;
  total_lines: number;
  subtotal_amount: number | string;
  items: CartItem[];
}

export interface CartCount {
  total_qty: number;
  total_lines: number;
}

export interface CartItemPayload {
  product_id: ID;
  qty?: number;
}

export interface CartUpdatePayload {
  qty: number;
}
