import AuthenticatedSessionController from './AuthenticatedSessionController';
import ConfirmablePasswordController from './ConfirmablePasswordController';
import EmailVerificationNotificationController from './EmailVerificationNotificationController';
import EmailVerificationPromptController from './EmailVerificationPromptController';
import NewPasswordController from './NewPasswordController';
import PasswordResetLinkController from './PasswordResetLinkController';
import VerifyEmailController from './VerifyEmailController';

const Auth = {
    AuthenticatedSessionController,
    PasswordResetLinkController,
    NewPasswordController,
    EmailVerificationPromptController,
    VerifyEmailController,
    EmailVerificationNotificationController,
    ConfirmablePasswordController,
};

export default Auth;
