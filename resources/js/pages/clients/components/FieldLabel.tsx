import type { ReactNode } from 'react';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

type Props = {
    htmlFor: string;
    children: ReactNode;
    required?: boolean;
    className?: string;
};

export default function FieldLabel({
    htmlFor,
    children,
    required = false,
    className,
}: Props) {
    return (
        <Label htmlFor={htmlFor} className={cn(className)}>
            {children}
            {required && (
                <span className="text-destructive" aria-hidden="true">
                    {' '}
                    *
                </span>
            )}
        </Label>
    );
}
