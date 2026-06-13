import { z } from "zod";

export const categoryFormSchema = z.object({
  name: z
    .string()
    .trim()
    .min(2, "Category name must be at least 2 characters.")
    .max(120, "Category name is too long."),
  slug: z
    .string()
    .trim()
    .min(2, "Slug must be at least 2 characters.")
    .max(160, "Slug is too long."),
  description: z.string().optional().or(z.literal("")),
  icon: z.string().max(100, "Icon is too long.").optional().or(z.literal("")),
  display: z.enum(["1", "0"], {
    message: "Display flag is required.",
  }),
  sort: z
    .string()
    .min(1, "Sort order is required.")
    .refine((value) => /^-?\d+$/.test(value), "Sort order must be an integer."),
});

export type CategoryFormSchemaType = z.infer<typeof categoryFormSchema>;
