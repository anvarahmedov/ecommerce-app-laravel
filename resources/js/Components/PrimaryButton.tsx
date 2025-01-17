import { ButtonHTMLAttributes } from 'react';

export default function PrimaryButton({
    className = '',
    disabled,
    children,
    ...props
}: ButtonHTMLAttributes<HTMLButtonElement>) {
    return (
        <button
            {...props}
            className={
                `btn btn-primary bg-emerald-500 hover:bg-emerald-400 border-0 rounded-xl` + className
            }
            disabled={disabled}
        >
            {children}
        </button>
    );
}
