import { z } from "zod";

export const articleFormSchema = z.object({
  title: z.string().trim().min(2, "Title must be at least 2 characters.").max(180, "Title is too long."),
  slug: z.string().trim().min(2, "Slug must be at least 2 characters.").max(220, "Slug is too long."),
  category: z.string().trim().min(2, "Category must be at least 2 characters.").max(100, "Category is too long."),
  excerpt: z.string().optional().or(z.literal("")),
  content: z.string().optional().or(z.literal("")),
  author_name: z.string().max(120, "Author name is too long.").optional().or(z.literal("")),
  status: z.enum(["draft", "published"], {
    message: "Status is required.",
  }),
  published_at: z.string().optional().or(z.literal("")),
  read_time: z.string().max(50, "Read time is too long.").optional().or(z.literal("")),
});

export type ArticleFormSchemaType = z.infer<typeof articleFormSchema>;
