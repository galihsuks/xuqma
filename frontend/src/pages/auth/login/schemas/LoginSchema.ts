import { z } from "zod";

export const loginSchema = z.object({
  username: z
    .string()
    .min(3, "Username / Email must be at least 3 characters.")
    .max(100, "Username / Email cannot exceed 100 characters."),
  password: z
    .string()
    .min(6, "Password must be at least 6 characters.")
    .max(255, "Password cannot exceed 255 characters."),
});

export type LoginSchemaType = z.infer<typeof loginSchema>;
