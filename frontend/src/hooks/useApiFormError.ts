import { useNotificationStore } from "../store/notifStore";
import { sendFrontendErrorLog } from "../utils/frontendErrorLog";

type ErrorContext = Record<string, unknown>;

interface UseApiFormErrorOptions {
  defaultMessage?: string;
  logEvent?: string;
}

export const useApiFormError = (options?: UseApiFormErrorOptions) => {
  const { addToast } = useNotificationStore();
  const defaultMessage = options?.defaultMessage ?? "Request failed";
  const logEvent = options?.logEvent ?? "form_submit_failed";

  const handleApiFormError = (error: unknown, context?: ErrorContext) => {
    const message = error instanceof Error ? error.message : defaultMessage;
    void sendFrontendErrorLog(logEvent, {
      ...(context ?? {}),
      error_message: message,
    });
    addToast(message, "error");
  };

  return { handleApiFormError };
};
