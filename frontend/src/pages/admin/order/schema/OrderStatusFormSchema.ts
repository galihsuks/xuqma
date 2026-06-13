import { z } from "zod";

export const orderStatusFormSchema = z.object({
  status: z.enum(["Waiting Payment", "Processing", "Packed", "Shipped", "Completed", "Cancelled"], {
    message: "Order status is required.",
  }),
  payment_status: z.enum(["Unpaid", "Paid", "Refunded"], {
    message: "Payment status is required.",
  }),
  notes: z.string().max(1000, "Notes are too long.").optional().or(z.literal("")),
});

export type OrderStatusFormSchemaType = z.infer<typeof orderStatusFormSchema>;
