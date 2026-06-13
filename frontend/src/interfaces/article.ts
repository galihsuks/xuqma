import type { ID, KeywordPaginationQuery } from "./common";

export interface Article {
  id: ID;
  title: string;
  slug: string;
  category: string;
  excerpt: string | null;
  content?: string | null;
  author_name: string | null;
  status: "draft" | "published";
  published_at: string | null;
  read_time: string | null;
  created_at?: string;
  updated_at?: string;
}

export interface ArticlePayload {
  title: string;
  slug: string;
  category: string;
  excerpt?: string | null;
  content?: string | null;
  author_name?: string | null;
  status: "draft" | "published";
  published_at?: string | null;
  read_time?: string | null;
}

export interface ArticleListQuery extends KeywordPaginationQuery {
  status?: "" | "draft" | "published";
}
