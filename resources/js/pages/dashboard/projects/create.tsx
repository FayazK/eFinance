import AppLayout from '@/layouts/app-layout';
import { Card } from 'antd';
import ProjectForm from './partials/project-form';

export default function CreateProject() {
    return (
        <AppLayout pageTitle="Create New Project">
            <Card>
                <ProjectForm />
            </Card>
        </AppLayout>
    );
}
