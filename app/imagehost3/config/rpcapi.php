<?php
\webcitron\Subframe\RpcApiController::allow('imagehost2.box.artifact.Stream.getGrid');
\webcitron\Subframe\RpcApiController::allow('imagehost3.box.artifact.Stream.getGrid');

\webcitron\Subframe\RpcApiController::allow('backend.user.UserController.signUp');
\webcitron\Subframe\RpcApiController::allow('backend.user.UserController.signIn');
\webcitron\Subframe\RpcApiController::allow('backend.user.UserController.resetPassword');
\webcitron\Subframe\RpcApiController::allow('backend.user.UserController.changeEmail');
\webcitron\Subframe\RpcApiController::allow('backend.user.UserController.changePassword');
\webcitron\Subframe\RpcApiController::allow('backend.user.UserController.signInWithFacebook');
\webcitron\Subframe\RpcApiController::allow('backend.user.UserController.removeLoggedUserArtifact');
\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.upload');
\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.reportAbuse');
\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.getImageElementBase64');
\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.getCommercialGalleryCode');

\webcitron\Subframe\RpcApiController::allow('backend.artifact.MemeBackgroundController.getMostPopular');
//\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.getRandomMemBackground');
//\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.getMemsBackgroundsBySearch');
///* test app */ \webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.getStats');
\webcitron\Subframe\RpcApiController::allow('backend.feedback.FeedbackController.send');
\webcitron\Subframe\RpcApiController::allow('backend.feedback.FeedbackController.sendAdvertisement');
\webcitron\Subframe\RpcApiController::allow('backend.YouTube.parseUrl');

\webcitron\Subframe\RpcApiController::allow('backend.stats.StatsController.getStatsUserGeneral');
\webcitron\Subframe\RpcApiController::allow('backend.stats.StatsController.getStatsUserArtifact');

\webcitron\Subframe\RpcApiController::setErrorHandler('\\backend\\ErrorHandler::rpcError');


//\webcitron\Subframe\RpcApiController::allow('artifact.StoryController.upload');
\webcitron\Subframe\RpcApiController::allow('backend.newsletter.NewsletterController.subscribe');