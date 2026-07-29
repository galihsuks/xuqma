import type { BaseSyntheticEvent } from "react";
import type { Control } from "react-hook-form";
import { Button, FormInput } from "../../../../components/ui";
import { AppLogo } from "../../../../components/shared/AppLogo";
import type { LoginSchemaType } from "../schemas/LoginSchema";

interface LoginFormProps {
  control: Control<LoginSchemaType>;
  onSubmit: (event?: BaseSyntheticEvent) => void;
  loading: boolean;
}

export const LoginForm = ({ control, onSubmit, loading }: LoginFormProps) => {
  return (
    <main className="relative grid min-h-screen place-items-center overflow-hidden bg-slate-100 p-4">
      <div className="pointer-events-none absolute -top-20 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-primary-300/40 blur-3xl" />
      <div className="pointer-events-none absolute -bottom-20 right-0 h-72 w-72 rounded-full bg-secondary-300/40 blur-3xl" />

      <form
        className="relative z-10 grid w-full max-w-5xl overflow-hidden rounded-3xl border border-primary-100 bg-white shadow-[0_20px_60px_-30px_rgba(236,72,153,0.45)] md:grid-cols-2"
        onSubmit={onSubmit}
      >
        <aside className="hidden h-full flex-col justify-between bg-gradient-to-br from-primary-950 via-primary-900 to-secondary-400 p-12 text-white md:flex">
          <div>
            <div className="inline-flex h-12 w-12 items-center justify-center">
              <AppLogo variant="icon" className="h-10 w-10" />
            </div>
            <p className="text-sm uppercase tracking-[0.22em] text-white/80">IT Commerce</p>
          </div>
          <div>
            <h2 className="mt-4 text-4xl font-semibold leading-tight max-w-[350px]">
              The latest software and hardware are here
            </h2>
            <p className="mt-4 max-w-sm text-sm text-white/85">
              SEO storefront pages can stay on CodeIgniter, while transactional customer and admin
              experiences live under React.
            </p>
          </div>
        </aside>

        <section className="p-7 sm:p-12 flex flex-col justify-center">
          <AppLogo variant="landscape" className="h-13 w-42 mb-4 block md:hidden" />
          <h1 className="text-3xl font-semibold text-slate-800">Welcome back</h1>
          <p className="mt-1 text-sm text-slate-500">Sign in to continue shopping</p>

          <div className="mt-6">
            <FormInput
              control={control}
              name="username"
              label="Username / Email"
              type="text"
              placeholder="Enter your username or email"
            />
          </div>

          <div className="mt-4">
            <FormInput
              control={control}
              name="password"
              label="Password"
              type="password"
              placeholder="Enter your password"
            />
          </div>

          <Button type="submit" buttonType="submit" loading={loading} className="mt-6 w-full">
            {loading ? "Signing in..." : "Sign in"}
          </Button>
        </section>
      </form>
    </main>
  );
};
