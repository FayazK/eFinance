import AppLayout from '@/layouts/app-layout';
import ProjectForm from './partials/project-form';
import { Card } from 'antd';

export default function CreateProject() {
    return (
        <AppLayout pageTitle="Create New Project">
            <Card>
                <ProjectForm />
            </Card>
        </AppLayout>
    );
}
