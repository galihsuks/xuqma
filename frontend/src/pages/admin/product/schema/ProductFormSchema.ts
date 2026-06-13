import { z } from "zod";

export const productFormSchema = z.object({
  category_id: z.string().min(1, "Category is required."),
  sku: z.string().trim().min(2, "SKU must be at least 2 characters.").max(60, "SKU is too long."),
  name: z.string().trim().min(2, "Product name must be at least 2 characters.").max(160, "Product name is too long."),
  slug: z.string().trim().min(2, "Slug must be at least 2 characters.").max(190, "Slug is too long."),
  summary: z.string().optional().or(z.literal("")),
  description: z.string().optional().or(z.literal("")),
  highlight: z.string().max(255, "Highlight is too long.").optional().or(z.literal("")),
  price: z
    .string()
    .min(1, "Price is required.")
    .refine((value) => !Number.isNaN(Number(value)) && Number(value) >= 0, "Price must be a valid number."),
  stock: z
    .string()
    .min(1, "Stock is required.")
    .refine((value) => /^-?\d+$/.test(value), "Stock must be an integer."),
  stock_badge: z.enum(["Ready Stock", "Pre Order", "Limited"], {
    message: "Stock badge is required.",
  }),
  is_featured: z.enum(["1", "0"], {
    message: "Featured flag is required.",
  }),
  display: z.enum(["1", "0"], {
    message: "Display flag is required.",
  }),
  sort: z
    .string()
    .min(1, "Sort order is required.")
    .refine((value) => /^-?\d+$/.test(value), "Sort order must be an integer."),
});

export type ProductFormSchemaType = z.infer<typeof productFormSchema>;
