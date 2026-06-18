<?php

namespace App\Enums;

enum ApiResponseMessage: string
{
    case RegisterSuccess         = 'Registration successful.';
    case RegisterFailed          = 'Registration failed. Please try again.';
    case LoginSuccess            = 'Login successful.';
    case LogoutSuccess           = 'Logged out successfully.';
    case InvalidCredentials      = 'Invalid email or password.';
    case CreateSuccess           = 'Record created successfully.';
    case UpdateSuccess           = 'Record updated successfully.';
    case DeleteSuccess           = 'Record deleted successfully.';
    case NotFound                = 'Record not found.';
    case Unauthorized            = 'Unauthorized action.';
    case CategoryHasTransactions = 'Cannot delete category with existing transactions.';
    case CannotDeleteSelf        = 'You cannot delete your own admin account.';
    case GoogleOAuthFailed       = 'Google sign-in failed. Please try again.';
    case OnboardingComplete      = 'Setup completed successfully.';
    case AccountNotFound         = 'Account not found.';
    case BudgetNotFound          = 'Budget not found.';
    case TransactionNotFound     = 'Transaction not found.';
    case CategoryNotFound        = 'Category not found.';
    case ProfileUpdateSuccess    = 'Profile updated successfully.';
    case PasswordUpdateSuccess   = 'Password updated successfully.';
    case InvalidCurrentPassword  = 'Current password is incorrect.';
    case AccountArchived         = 'Account archived successfully.';
    case BudgetExists            = 'A budget for this category and period already exists.';
    case NotificationMarkRead    = 'Notifications marked as read.';
}
