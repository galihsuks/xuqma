import { useNavigate } from "react-router-dom";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { useAuthLoginMutation } from "../../../api/auth/authQuery";
import { useApiFormError } from "../../../hooks/useApiFormError";
import { queryClient } from "../../../lib/queryClient";
import { useAuthActions } from "../../../store/authStore";
import { useNotificationStore } from "../../../store/notifStore";
import { LoginForm } from "./components/LoginForm";
import { type LoginSchemaType, loginSchema } from "./schemas/LoginSchema";

export const LoginPage = () => {
  const { login } = useAuthActions();
  const navigate = useNavigate();
  const { addToast } = useNotificationStore();
  const { mutate: loginMutation, isPending: isLoginPending } = useAuthLoginMutation();
  const { handleApiFormError } = useApiFormError({ logEvent: "login_submit_failed" });

  const { control, handleSubmit } = useForm<LoginSchemaType>({
    resolver: zodResolver(loginSchema),
    defaultValues: {
      username: "",
      password: "",
    },
  });

  const onSubmit = (values: LoginSchemaType) => {
    loginMutation(values, {
      onSuccess: (response) => {
        if (!response.data) {
          const message = response.message || "Request failed";
          addToast(message, "error");
          return;
        }
        queryClient.clear();
        login(response.data.user, response.data.token);
        addToast(response.message, "success");
        navigate("/dashboard");
      },
      onError: (error) => {
        handleApiFormError(error, {
          username: values.username,
        });
      },
    });
  };

  return <LoginForm control={control} onSubmit={handleSubmit(onSubmit)} loading={isLoginPending} />;
};
