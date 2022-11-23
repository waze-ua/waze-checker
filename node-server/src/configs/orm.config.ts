import { MysqlConnectionOptions } from 'typeorm/driver/mysql/MysqlConnectionOptions';
import { BboxEntity } from 'src/bbox/bbox.entity';
import { RegionEntity } from 'src/region/region.entity';
import { ConfigService } from '@nestjs/config';

export const getOrmConfig = async (configService: ConfigService): Promise<MysqlConnectionOptions> => {
  return {
    type: configService.get('DB_TYPE'),
    host: configService.get('DB_HOST'),
    port: configService.get('DB_PORT'),
    username: configService.get('DB_USERNAME'),
    password: configService.get('DB_PASSWORD'),
    database: configService.get('DB_DATABASE'),
    entities: [BboxEntity, RegionEntity],
  };
};
