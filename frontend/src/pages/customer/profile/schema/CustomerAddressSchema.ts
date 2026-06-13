import { z } from "zod";

export const customerAddressSchema = z.object({
  label: z.string().trim().min(2, "Address label must be at least 2 characters.").max(60, "Address label is too long."),
  recipient_name: z
    .string()
    .trim()
    .min(2, "Recipient name must be at least 2 characters.")
    .max(120, "Recipient name is too long."),
  phone: z.string().trim().min(8, "Phone number must be at least 8 characters.").max(50, "Phone number is too long."),
  address: z.string().trim().min(10, "Address must be at least 10 characters.").max(500, "Address is too long."),
  is_default: z.enum(["1", "0"], {
    message: "Default flag is required.",
  }),
});

export type CustomerAddressSchemaType = z.infer<typeof customerAddressSchema>;
