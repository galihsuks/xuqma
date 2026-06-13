import { z } from "zod";

export const customerProfileSchema = z.object({
  preferred_phone: z
    .string()
    .trim()
    .min(8, "Phone number must be at least 8 characters.")
    .max(50, "Phone number is too long."),
  preferred_payment_method: z.enum(["Bank Transfer", "Virtual Account", "E-Wallet"], {
    message: "Preferred payment method is required.",
  }),
  preferred_courier_service: z.enum(["Instant Courier", "Regular Delivery", "Cargo Service"], {
    message: "Preferred courier service is required.",
  }),
});

export type CustomerProfileSchemaType = z.infer<typeof customerProfileSchema>;
