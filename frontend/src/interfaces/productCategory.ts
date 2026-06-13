import type { ID, KeywordPaginationQuery } from "./common";

export interface ProductCategory {
  id: ID;
  name: string;
  slug: string;
  description: string | null;
  icon: string | null;
  display: boolean | string;
  sort: number | string;
  created_at?: string;
  updated_at?: string;
}

export interface ProductCategoryPayload {
  name: string;
  slug: string;
  description?: string | null;
  icon?: string | null;
  display: 0 | 1 | "0" | "1";
  sort: number;
}

export interface ProductCategoryListQuery extends KeywordPaginationQuery {}
