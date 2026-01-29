import { useActivities } from '@/hooks/use-activities';
import { Activity, ActivityChange } from '@/types';
import {
    DeleteOutlined,
    EditOutlined,
    ExclamationCircleOutlined,
    HistoryOutlined,
    PlusCircleOutlined,
    UserOutlined,
} from '@ant-design/icons';
import { Alert, Avatar, Empty, Flex, Skeleton, Tag, theme, Timeline, Typography } from 'antd';

const { Text, Title } = Typography;
const { useToken } = theme;

interface ActivityTimelineProps {
    subjectType: string;
    subjectId: number;
}

function getEventIcon(event: string) {
    switch (event) {
        case 'created':
            return <PlusCircleOutlined />;
        case 'updated':
            return <EditOutlined />;
        case 'deleted':
            return <DeleteOutlined />;
        default:
            return <HistoryOutlined />;
    }
}

function getEventColor(event: string, token: ReturnType<typeof useToken>['token']) {
    switch (event) {
        case 'created':
            return token.colorSuccess;
        case 'updated':
            return token.colorInfo;
        case 'deleted':
            return token.colorError;
        default:
            return token.colorPrimary;
    }
}

function formatFieldName(field: string): string {
    return field
        .replace(/_/g, ' ')
        .replace(/([a-z])([A-Z])/g, '$1 $2')
        .replace(/\b\w/g, (char) => char.toUpperCase());
}

function formatValue(value: unknown): string {
    if (value === null || value === undefined) {
        return 'Empty';
    }
    if (typeof value === 'boolean') {
        return value ? 'Yes' : 'No';
    }
    if (typeof value === 'number') {
        return value.toLocaleString();
    }
    return String(value);
}

function ChangeItem({ change, token }: { change: ActivityChange; token: ReturnType<typeof useToken>['token'] }) {
    return (
        <Flex
            gap="small"
            align="center"
            style={{
                padding: '4px 8px',
                backgroundColor: token.colorBgTextHover,
                borderRadius: token.borderRadiusSM,
                fontSize: '12px',
            }}
        >
            <Text type="secondary" style={{ minWidth: '80px' }}>
                {formatFieldName(change.field)}:
            </Text>
            <Text
                delete
                type="secondary"
                style={{
                    color: token.colorTextDescription,
                }}
            >
                {formatValue(change.old)}
            </Text>
            <Text type="secondary">→</Text>
            <Text strong>{formatValue(change.new)}</Text>
        </Flex>
    );
}

function ActivityItem({ activity, token }: { activity: Activity; token: ReturnType<typeof useToken>['token'] }) {
    const hasChanges = activity.changes && activity.changes.length > 0;
    const hasCustomProperties = activity.properties.custom && Object.keys(activity.properties.custom).length > 0;

    return (
        <Flex vertical gap="small">
            <Flex gap="small" align="center">
                <Avatar
                    size="small"
                    src={activity.causer?.avatar_url}
                    icon={!activity.causer?.avatar_url && <UserOutlined />}
                    style={{ backgroundColor: token.colorPrimary }}
                />
                <Flex vertical gap={0}>
                    <Flex gap="small" align="center" wrap="wrap">
                        <Text strong style={{ fontSize: '13px' }}>
                            {activity.causer?.name || 'System'}
                        </Text>
                        <Text type="secondary" style={{ fontSize: '13px' }}>
                            {activity.description}
                        </Text>
                    </Flex>
                    <Text type="secondary" style={{ fontSize: '11px' }}>
                        {activity.created_at_human}
                    </Text>
                </Flex>
            </Flex>

            {hasChanges && (
                <Flex vertical gap="small" style={{ marginLeft: '32px' }}>
                    {activity.changes.map((change, index) => (
                        <ChangeItem key={`${change.field}-${index}`} change={change} token={token} />
                    ))}
                </Flex>
            )}

            {hasCustomProperties && (
                <Flex gap="small" wrap="wrap" style={{ marginLeft: '32px' }}>
                    {Object.entries(activity.properties.custom!).map(([key, value]) => (
                        <Tag key={key} color="blue">
                            {formatFieldName(key)}: {formatValue(value)}
                        </Tag>
                    ))}
                </Flex>
            )}
        </Flex>
    );
}

export default function ActivityTimeline({ subjectType, subjectId }: ActivityTimelineProps) {
    const { token } = useToken();
    const { activities, isLoading, error } = useActivities({
        subjectType,
        subjectId,
    });

    if (isLoading) {
        return (
            <Flex vertical gap="middle" style={{ padding: '16px 0' }}>
                {[1, 2, 3].map((i) => (
                    <Flex key={i} gap="middle" align="flex-start">
                        <Skeleton.Avatar active size="small" />
                        <Flex vertical gap="small" style={{ flex: 1 }}>
                            <Skeleton.Input active size="small" style={{ width: '60%' }} />
                            <Skeleton.Input active size="small" style={{ width: '40%' }} />
                        </Flex>
                    </Flex>
                ))}
            </Flex>
        );
    }

    if (error) {
        return (
            <Alert
                message="Failed to load activity"
                description={error}
                type="error"
                showIcon
                icon={<ExclamationCircleOutlined />}
            />
        );
    }

    if (activities.length === 0) {
        return (
            <Empty
                image={Empty.PRESENTED_IMAGE_SIMPLE}
                description={
                    <Flex vertical align="center" gap="small">
                        <Text type="secondary">No activity recorded yet</Text>
                        <Text type="secondary" style={{ fontSize: '12px' }}>
                            Changes to this record will appear here
                        </Text>
                    </Flex>
                }
                style={{ padding: '32px 0' }}
            />
        );
    }

    const timelineItems = activities.map((activity) => ({
        key: activity.id,
        dot: (
            <Flex
                align="center"
                justify="center"
                style={{
                    width: '24px',
                    height: '24px',
                    borderRadius: '50%',
                    backgroundColor: getEventColor(activity.event, token),
                    color: token.colorWhite,
                    fontSize: '12px',
                }}
            >
                {getEventIcon(activity.event)}
            </Flex>
        ),
        children: <ActivityItem activity={activity} token={token} />,
    }));

    return (
        <Flex vertical gap="middle">
            <Flex align="center" gap="small">
                <HistoryOutlined style={{ color: token.colorTextSecondary }} />
                <Title level={5} style={{ margin: 0 }}>
                    Activity History
                </Title>
                <Tag>{activities.length}</Tag>
            </Flex>
            <Timeline items={timelineItems} style={{ paddingTop: '8px' }} />
        </Flex>
    );
}
