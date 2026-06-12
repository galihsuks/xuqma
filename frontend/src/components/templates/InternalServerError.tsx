import { ServerCrash } from "lucide-react";
import { ErrorState } from "./ErrorState";

type InternalServerErrorProps = {
  onReset?: () => void;
};

const InternalServerError = ({ onReset }: InternalServerErrorProps) => {
  return (
    <ErrorState
      code="500"
      title="Internal Server Error"
      description="Something went wrong on the server while processing your request. Please try again in a moment."
      icon={ServerCrash}
      tone="danger"
      onReset={onReset}
      resetLabel="Try Again"
    />
  );
};

export default InternalServerError;
