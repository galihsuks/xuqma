import { z } from "zod";

export const customerCheckoutSchema = z.object({
  customer_phone: z
    .string()
    .trim()
    .min(8, "Phone number must be at least 8 characters.")
    .max(50, "Phone number is too long."),
  shipping_address: z
    .string()
    .trim()
    .min(10, "Shipping address must be at least 10 characters.")
    .max(500, "Shipping address is too long."),
  courier_service: z.enum(["Instant Courier", "Regular Delivery", "Cargo Service"], {
    message: "Courier service is required.",
  }),
  payment_method: z.enum(["Bank Transfer", "Virtual Account", "E-Wallet"], {
    message: "Payment method is required.",
  }),
  order_note: z.string().max(500, "Order note is too long.").optional().or(z.literal("")),
});

export type CustomerCheckoutSchemaType = z.infer<typeof customerCheckoutSchema>;
