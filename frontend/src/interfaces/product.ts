import type { ID, KeywordPaginationQuery } from "./common";

export interface Product {
  id: ID;
  category_id: ID;
  category_name: string;
  category_slug: string;
  sku: string;
  name: string;
  slug: string;
  summary: string | null;
  description?: string | null;
  highlight: string | null;
  price: number | string;
  stock: number | string;
  stock_badge: "Ready Stock" | "Pre Order" | "Limited";
  is_featured: boolean | string;
  display: boolean | string;
  sort: number | string;
  created_at?: string;
  updated_at?: string;
}

export interface ProductPayload {
  category_id: ID;
  sku: string;
  name: string;
  slug: string;
  summary?: string | null;
  description?: string | null;
  highlight?: string | null;
  price: number | string;
  stock: number | string;
  stock_badge: "Ready Stock" | "Pre Order" | "Limited";
  is_featured: 0 | 1 | "0" | "1";
  display: 0 | 1 | "0" | "1";
  sort: number;
}

export interface ProductListQuery extends KeywordPaginationQuery {
  category_id?: ID;
}
