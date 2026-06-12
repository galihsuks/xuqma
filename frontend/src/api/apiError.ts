import axios from "axios";

type ErrorData = {
  message?: string;
  data?: {
    message?: string;
  };
};

export const getApiErrorMessage = (error: unknown): string => {
  if (axios.isAxiosError<ErrorData>(error)) {
    return (
      error.response?.data?.message ??
      error.response?.data?.data?.message ??
      error.message ??
      "Request failed"
    );
  }

  if (error instanceof Error) {
    return error.message;
  }

  return "Request failed";
};
