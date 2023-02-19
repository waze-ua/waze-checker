import { ConfigService } from '@nestjs/config';
import { MysqlConnectionOptions } from 'typeorm/driver/mysql/MysqlConnectionOptions';
import { BboxEntity } from 'src/bbox/bbox.entity';
import { RegionEntity } from 'src/region/region.entity';
import { UserEntity } from 'src/user/user.entity';
import { StreetEntity } from 'src/street/street.entity';
import { SegmentEntity } from 'src/segment/segment.entity';
import { ConnectionEntity } from 'src/connection/connection.entity';
import { RoadTypeEntity } from 'src/roadType/roadType.entity';
import { KeyValueEntity } from 'src/keyValue/keyValue.entity';

export const getOrmConfig = async (
  configService: ConfigService,
): Promise<MysqlConnectionOptions> => {
  return {
    type: configService.get('DB_TYPE'),
    host: configService.get('DB_HOST'),
    port: configService.get('DB_PORT'),
    username: configService.get('DB_USERNAME'),
    password: configService.get('DB_PASSWORD'),
    database: configService.get('DB_DATABASE'),
    synchronize: true,
    entities: [
      BboxEntity,
      ConnectionEntity,
      KeyValueEntity,
      RegionEntity,
      RoadTypeEntity,
      SegmentEntity,
      StreetEntity,
      UserEntity,
    ],
  };
};