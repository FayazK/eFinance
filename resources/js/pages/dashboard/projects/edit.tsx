import AppLayout from '@/layouts/app-layout';
import { Project } from '@/types';
import { Card } from 'antd';
import ProjectForm from './partials/project-form';

interface EditProjectProps {
    project: Project;
}

export default function EditProject({ project }: EditProjectProps) {
    return (
        <AppLayout pageTitle="Edit Project">
            <Card>
                <ProjectForm project={project} isEdit={true} />
            </Card>
        </AppLayout>
    );
}
