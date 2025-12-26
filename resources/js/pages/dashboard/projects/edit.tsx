import AppLayout from '@/layouts/app-layout';
import ProjectForm from './partials/project-form';
import { Card } from 'antd';
import { Project } from '@/types';

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
