import GalleryController from './GalleryController'
import ProjectController from './ProjectController'
import ProjectSandboxController from './ProjectSandboxController'
import ProjectVerificationController from './ProjectVerificationController'
import PromptController from './PromptController'
import ContainerController from './ContainerController'
import CommentController from './CommentController'
import BalanceController from './BalanceController'
import DockerSystemController from './DockerSystemController'
import ProjectDeploymentController from './ProjectDeploymentController'
import SubdomainController from './SubdomainController'
import ChatController from './ChatController'
import Settings from './Settings'
import Auth from './Auth'

const Controllers = {
    GalleryController: Object.assign(GalleryController, GalleryController),
    ProjectController: Object.assign(ProjectController, ProjectController),
    ProjectSandboxController: Object.assign(ProjectSandboxController, ProjectSandboxController),
    ProjectVerificationController: Object.assign(ProjectVerificationController, ProjectVerificationController),
    PromptController: Object.assign(PromptController, PromptController),
    ContainerController: Object.assign(ContainerController, ContainerController),
    CommentController: Object.assign(CommentController, CommentController),
    BalanceController: Object.assign(BalanceController, BalanceController),
    DockerSystemController: Object.assign(DockerSystemController, DockerSystemController),
    ProjectDeploymentController: Object.assign(ProjectDeploymentController, ProjectDeploymentController),
    SubdomainController: Object.assign(SubdomainController, SubdomainController),
    ChatController: Object.assign(ChatController, ChatController),
    Settings: Object.assign(Settings, Settings),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers